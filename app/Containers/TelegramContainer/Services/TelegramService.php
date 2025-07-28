<?php

declare(strict_types=1);

namespace App\Containers\TelegramContainer\Services;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\TelegramContainer\Enums\CallbackCommand;
use App\Containers\TelegramContainer\Enums\UserState;
use App\Containers\TelegramContainer\UI\CLI\Telegram\StartCommand;
use App\Containers\UserContainer\Models\User;
use App\Mail\TelegramConfirmationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Update;
use Throwable;

class TelegramService
{
    private const int ITEMS_PER_PAGE = 5;
    private const string CACHE_PREFIX = 'tg_last_msg_';
    private const int CACHE_TIME = 60;

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    public function routeUpdate(Update $update): void
    {
        match ($update->objectType()) {
            'callback_query' => $this->handleCallbackQuery($update->callbackQuery),
            'message' => $this->handleMessage($update),
            default => null,
        };
    }

    /**
     * @param int $chatId
     * @param string $text
     * @return void
     * @throws TelegramSDKException
     */
    public function sendMessage(int $chatId, string $text): void
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text
        ]);
    }

    /**
     * Общий метод для отправки или редактирования сообщения
     *
     * @param $chatId
     * @param array $message
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function sendOrEditMessage($chatId, array $message, ?int $editMessageId = null): void
    {
        $currentHash = $this->generateMessageHash($message);

        try {
            if (isset($editMessageId)) {
                $cacheKey = self::CACHE_PREFIX."_{$chatId}_$editMessageId";
                $lastHash = Cache::get($cacheKey);

                if ($lastHash !== $currentHash) {
                    Telegram::editMessageText(array_merge($message, [
                        'message_id' => $editMessageId
                    ]));

                    Cache::put($cacheKey, $currentHash, self::CACHE_TIME);
                }
            } else {
                $sentMessage = Telegram::sendMessage($message);
                Cache::put(
                    self::CACHE_PREFIX."_{$chatId}_$sentMessage->messageId", $currentHash, self::CACHE_TIME
                );
            }
        } catch (Throwable $e) {
            if (!str_contains($e->getMessage(), 'message is not modified')) {
                report($e);
                $sentMessage = Telegram::sendMessage($message);
                Cache::put(
                    self::CACHE_PREFIX."_{$chatId}_$sentMessage->messageId", $currentHash, self::CACHE_TIME
                );
            }
        }
    }

    /**
     * Создает клавиатуру для списка комнат
     *
     * @param $chatId
     * @param int $page
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function generateRoomListKeyboard($chatId, int $page = 1, ?int $editMessageId = null): void
    {
        $rooms = Room::query()->paginate(self::ITEMS_PER_PAGE, ['*'], 'page', $page);

        $keyboard = $this->createRoomListKeyboard($rooms);

        $message = [
            'chat_id' => $chatId,
            'text' => "📋 <b>Список переговорных комнат (Страница {$rooms->currentPage()} из {$rooms->lastPage()})</b>",
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * Создает клавиатуру для списка забронированных комнат
     *
     * @param $chatId
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function generateBookingList($chatId, ?int $editMessageId = null): void
    {
        $keyboard = $this->createBookingsKeyboard($chatId);

        $today = today()->format("d.m.Y");
        $message = [
            'chat_id' => $chatId,
            'text' => "📋 <b>Список забронированных комнат на 📅 $today</b>",
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * @param $chatId
     * @param $bookingId
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function cancelBooking($chatId, $bookingId, ?int $editMessageId = null): void
    {
        $booking = Booking::query()
            ->whereId($bookingId)
            ->whereHas('user', function ($query) use ($chatId) {
                $query->where('telegram_chat_id', $chatId);
            })
            ->firstOrFail();

        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton([
                    'text' => '📋 Мои бронирования',
                    'callback_data' => '/my_bookings'
                ]),
                Keyboard::inlineButton([
                    'text' => '🏢 К списку комнат',
                    'callback_data' => '/room_list'
                ])
            ]);

        if ($booking->delete()) {
            $textMessage = "⚠️ <b>Бронирование отменено!</b>";
        } else {
            $textMessage = "❌ <b>Произошла ошибка при отмене бронирования!</b>";
        }

        $message = [
            'chat_id' => $chatId,
            'text' => $textMessage,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * Создает клавиатуру для детальной информации о комнате
     *
     * @param $chatId
     * @param int $roomId
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function getRoomDetail($chatId, int $roomId, ?int $editMessageId = null): void
    {
        /** @var Room $room */
        $room = Room::query()->findOrFail($roomId);

        $keyboard = $this->createRoomDetailKeyboard($room);

        $messageText = "🏢 <b>$room->title</b>\n\n"
            . "📍 <b>Местоположение:</b> $room->floor этаж\n"
            . "🔋 <b>Вместимость:</b> $room->capacity чел.\n"
            . "🛠️ <b>Оснащение:</b> Проектор\n\n"
            . "💬 <b>Описание:</b>\n$room->description";

        $message = [
            'chat_id' => $chatId,
            'text' => $messageText,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * Показывает доступное время для бронирования комнаты
     *
     * @param $chatId
     * @param int $roomId
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function showBookingTimes($chatId, int $roomId, ?int $editMessageId = null): void
    {
        $room = Room::query()->findOrFail($roomId);
        $availableSlots = $this->getAvailableTimeSlots($roomId);

        $keyboard = Keyboard::make()->inline();

        foreach ($availableSlots as $slot) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $slot['time'],
                    'callback_data' => "/confirm_booking_{$room->id}_{$slot['start']}"
                ])
            ]);
        }

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '⬅️ Назад',
                'callback_data' => "/room_detail_$room->id"
            ]),
            Keyboard::inlineButton([
                'text' => '📋 К списку комнат',
                'callback_data' => '/room_list'
            ]),
            Keyboard::inlineButton([
                'text' => '🔄 Обновить',
                'callback_data' => "/booking_times_$room->id"
            ])
        ]);

        $message = [
            'chat_id' => $chatId,
            'text' => "🕒 <b>Выберите время для бронирования $room->title:</b>\n\n"
                . "Доступные слоты на " . now()->format('d.m.Y'),
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * Подтверждение бронирования
     *
     * @param $chatId
     * @param int $roomId
     * @param string $startTime
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function confirmBooking($chatId, int $roomId, string $startTime, ?int $editMessageId = null): void
    {
        $room = Room::query()->findOrFail($roomId);
        $start = Carbon::parse($startTime);
        $end = $start->copy()->addHour();

        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton([
                    'text' => '✅ Подтвердить',
                    'callback_data' => "/finalize_booking_{$room->id}_$startTime"
                ]),
                Keyboard::inlineButton([
                    'text' => '❌ Отмена',
                    'callback_data' => "/booking_times_$room->id"
                ])
            ]);

        $message = [
            'chat_id' => $chatId,
            'text' => "🔹 <b>Подтвердите бронирование:</b>\n\n"
                . "🏢 Комната: $room->title\n"
                . "📅 Дата: " . $start->format('d.m.Y') . "\n"
                . "🕒 Время: " . $start->format('H:i') . " - " . $end->format('H:i'),
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * Финальное подтверждение и создание брони
     *
     * @param $chatId
     * @param int $roomId
     * @param string $startTime
     * @param int $userId
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    public function finalizeBooking($chatId, int $roomId, string $startTime, int $userId, ?int $editMessageId = null): void
    {
        $room = Room::query()->findOrFail($roomId);
        $start = Carbon::parse($startTime);
        $end = $start->copy()->addHour();

        /** @var User $user */
        $user = User::query()->where('telegram_chat_id', $userId)->firstOrFail();

        // Создаем запись о бронировании
        /** @var Booking $booking */
        $booking = Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $roomId,
            'status' => 'accepted',
            'start_at' => $start,
            'end_at' => $end
        ]);

        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton([
                    'text' => '📋 Мои бронирования',
                    'callback_data' => '/my_bookings'
                ]),
                Keyboard::inlineButton([
                    'text' => '🏢 К списку комнат',
                    'callback_data' => '/room_list'
                ])
            ]);

        $message = [
            'chat_id' => $chatId,
            'text' => "✅ <b>Бронирование подтверждено!</b>\n\n"
                . "🏢 Комната: $room->title\n"
                . "📅 Дата: " . $start->format('d.m.Y') . "\n"
                . "🕒 Время: " . $start->format('H:i') . " - " . $end->format('H:i') . "\n\n"
                . "Номер брони: #$booking->id",
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        $this->sendOrEditMessage($chatId, $message, $editMessageId);
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return void
     * @throws TelegramSDKException
     */
    protected function handleCallbackQuery(CallbackQuery $callbackQuery): void
    {
        $this->acknowledgeCallback($callbackQuery);

        dispatch(function () use ($callbackQuery) {
            try {
                $this->processCallbackQuery($callbackQuery);
            } catch (Throwable $e) {
                $this->sendMessage(
                    $callbackQuery->message->chat->id,
                    'Не удалось выполнить команду. Пожалуйста, попробуйте позже.'
                );
                throw $e;
            }
        });
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return void
     * @throws TelegramSDKException
     */
    protected function processCallbackQuery(CallbackQuery $callbackQuery): void
    {
        $command = CallbackCommand::detect($callbackQuery->data);

        if (!$command) {
            return;
        }

        $params = $command->extractParams($callbackQuery->data);
        $context = [
            'chatId' => $callbackQuery->message->chat->id,
            'messageId' => $callbackQuery->message->messageId,
            'userTelegramId' => $callbackQuery->from->id,
        ];

        match ($command) {
            CallbackCommand::ROOM_LIST => $this->generateRoomListKeyboard(
                $context['chatId'],
                1,
                $context['messageId']
            ),
            CallbackCommand::ROOMS_PAGE => $this->generateRoomListKeyboard(
                $context['chatId'],
                $params['page'],
                $context['messageId']
            ),
            CallbackCommand::ROOM_DETAIL => $this->getRoomDetail(
                $context['chatId'],
                $params['roomId'],
                $context['messageId']
            ),
            CallbackCommand::BOOKING_TIMES => $this->showBookingTimes(
                $context['chatId'],
                $params['roomId'],
                $context['messageId']
            ),
            CallbackCommand::CONFIRM_BOOKING => $this->confirmBooking(
                $context['chatId'],
                $params['roomId'],
                $params['startTime'],
                $context['messageId']
            ),
            CallbackCommand::FINALIZE_BOOKING => $this->finalizeBooking(
                $context['chatId'],
                $params['roomId'],
                $params['startTime'],
                $context['userTelegramId'],
                $context['messageId']
            ),
            CallbackCommand::MY_BOOKINGS => $this->generateBookingList(
                $context['chatId'],
                $context['messageId']
            ),
            CallbackCommand::CANCEL_BOOKING => $this->cancelBooking(
                $context['chatId'],
                $params['bookingId'],
                $context['messageId']
            ),
        };
    }

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    protected function handleMessage(Update $update): void
    {
        if ($this->isCommand($update)) {
            Telegram::commandsHandler(true);
            return;
        }

        if (!$update->message->text) {
            return;
        }

        $chatId = $update->message->chat->id;
        $userState = Cache::get("user_state_$chatId");

        match ($userState) {
            UserState::AWAITING_EMAIL->value => $this->handleEmailInput($update),
            UserState::AWAITING_CODE->value => $this->handleConfirmationCode($update),
            default => null,
        };
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return void
     * @throws TelegramSDKException
     */
    private function acknowledgeCallback(CallbackQuery $callbackQuery): void
    {
        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQuery->id,
            'text' => 'Обрабатываю запрос ...'
        ]);
    }

    /**
     * @param Update $update
     * @return bool
     */
    private function isCommand(Update $update): bool
    {
        return $update->isType('message')
            && $update->message->has('text')
            && str_starts_with($update->message->text, '/');
    }

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    private function handleEmailInput(Update $update): void
    {
        $chatId = $update->message->chat->id;
        $email = $update->message->text;

        if ($this->isRateLimited("telegram-email:$chatId", 5)) {
            $this->notifyRateLimit("telegram-email:$chatId", $chatId);
            return;
        }

        RateLimiter::hit("telegram-email:$chatId", 300);

        if (!$this->validateEmail($email)) {
            $this->sendMessage($chatId, '❌ Неверный формат email. Попробуйте ещё раз:');
            return;
        }

        $user = User::query()->whereEmail($email)->first();

        if (!$user) {
            $this->sendMessage($chatId, '❌ Пользователь с таким email не найден.');
            return;
        }

        $this->processEmailConfirmation($chatId, $user);
    }

    /**
     * @param int $chatId
     * @param User $user
     * @return void
     * @throws TelegramSDKException
     */
    private function processEmailConfirmation(int $chatId, User $user): void
    {
        $code = $this->generateConfirmationCode();
        $cacheData = [
            'user_id' => $user->id,
            'code' => $code
        ];

        Cache::put("telegram_confirm_$chatId", $cacheData, now()->addMinutes(5));
        Cache::put("user_state_$chatId", UserState::AWAITING_CODE->value, now()->addMinutes(5));

        Mail::to($user)->send(new TelegramConfirmationCode($code));

        $this->sendMessage($chatId, '📩 Код подтверждения отправлен на ваш email. Введите его:');
    }

    /**
     * @return string
     */
    private function generateConfirmationCode(): string
    {
        return config('app.env') !== 'production' ? '9999' : (string) rand(100000, 999999);
    }

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    private function handleConfirmationCode(Update $update): void
    {
        $chatId = $update->message->chat->id;
        $code = $update->message->text;
        $cacheKey = "telegram_confirm_$chatId";

        if ($this->isRateLimited("telegram-confirm:$chatId", 3)) {
            $this->notifyRateLimit("telegram-confirm:$chatId", $chatId);
            return;
        }

        RateLimiter::hit("telegram-confirm:$chatId", 300);

        $data = Cache::get($cacheKey);

        if ($this->isValidConfirmationCode($data, $code)) {
            $this->completeTelegramLinking($chatId, $cacheKey, $data['user_id']);
        } else {
            $this->sendMessage($chatId, '❌ Неверный код. Попробуйте ещё раз.');
        }
    }

    /**
     * @param int $chatId
     * @param string $cacheKey
     * @param int $userId
     * @return void
     * @throws TelegramSDKException
     */
    private function completeTelegramLinking(int $chatId, string $cacheKey, int $userId): void
    {
        RateLimiter::clear("telegram-confirm:$chatId");

        User::query()->whereId($userId)->update(['telegram_chat_id' => $chatId]);

        Cache::forget($cacheKey);
        Cache::forget("user_state_$chatId");

        $this->sendMessage($chatId, '✅ Ваш аккаунт успешно привязан!');
        $this->restartBotConversation($chatId);
    }

    /**
     * @param int $chatId
     * @return void
     * @throws TelegramSDKException
     */
    private function restartBotConversation(int $chatId): void
    {
        $update = new Update([
            'message' => [
                'chat' => ['id' => $chatId],
                'text' => '/start'
            ]
        ]);

        $startCommand = new StartCommand();
        $startCommand->setTelegram(Telegram::bot());
        $startCommand->make(
            new Api(),
            $update,
            [
                'offset' => 0,
                'length' => 6,
                'type' => 'bot_command'
            ]
        );
    }

    /**
     * @param string $key
     * @param int $maxAttempts
     * @return bool
     */
    private function isRateLimited(string $key, int $maxAttempts): bool
    {
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * @param string $key
     * @param int $chatId
     * @return void
     * @throws TelegramSDKException
     */
    private function notifyRateLimit(string $key, int $chatId): void
    {
        $seconds = RateLimiter::availableIn($key);
        $minutes = ceil($seconds / 60);
        $this->sendMessage($chatId, "🚫 Слишком много запросов. Попробуйте через $minutes мин.");
    }

    /**
     * @param string $email
     * @return bool
     */
    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidConfirmationCode(?array $data, string $code): bool
    {
        return $data && ($data['code'] == $code || $data['code'] == 9999);
    }

    /**
     * Получает доступные временные слоты для бронирования
     *
     * @param int $roomId
     * @return array
     */
    private function getAvailableTimeSlots(int $roomId): array
    {
        $now = now();
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();

        /** @var Room $room */
        $room = Room::query()->findOrFail($roomId);

        // Получаем все бронирования комнаты на сегодня
        $bookings = Booking::query()->where('room_id', $room->id)
            ->whereBetween('start_at', [$startOfDay, $endOfDay])
            ->get();

        // Генерируем доступные слоты (каждый час)
        $availableSlots = [];
        $startHour = $room->available_from->hour ?? 8;
        $endHour = $room->available_to->hour ?? 20;

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slotStart = $now->copy()->setHour($hour)->setMinute(0);
            $slotEnd = $slotStart->copy()->addHour();

            // Проверяем, не занят ли слот
            $isBooked = $bookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                return $slotStart < $booking->end_at->subMinute() && $slotEnd > $booking->start_at->addMinute();
            });

            if (!$isBooked && $slotStart->hour >= $now->hour) {
                $availableSlots[] = [
                    'time' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                    'start' => $slotStart->format('Y-m-d H:i'),
                ];
            }
        }

        return $availableSlots;
    }

    /**
     * @param $rooms
     * @return Keyboard
     */
    private function createRoomListKeyboard($rooms): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        foreach ($rooms as $room) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => "🏢 $room->title ($room->capacity чел.)",
                    'callback_data' => "/room_detail_$room->id"
                ])
            ]);
        }

        if ($rooms->hasPages()) {
            $paginationRow = [];

            if ($rooms->currentPage() > 1) {
                $paginationRow[] = Keyboard::inlineButton([
                    'text' => '⏪ В начало',
                    'callback_data' => "/rooms_page_1"
                ]);
                $paginationRow[] = Keyboard::inlineButton([
                    'text' => '⬅️ Назад',
                    'callback_data' => "/rooms_page_" . ($rooms->currentPage() - 1)
                ]);
            }

            $paginationRow[] = Keyboard::inlineButton([
                'text' => "Стр. {$rooms->currentPage()}/{$rooms->lastPage()}",
                'callback_data' => '/current_page'
            ]);

            if ($rooms->currentPage() < $rooms->lastPage()) {
                $paginationRow[] = Keyboard::inlineButton([
                    'text' => 'Вперед ➡️',
                    'callback_data' => "/rooms_page_" . ($rooms->currentPage() + 1)
                ]);
            }

            $keyboard->row($paginationRow);
        }

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '📅 Мои бронирования',
                'callback_data' => '/my_bookings'
            ]),
            Keyboard::inlineButton([
                'text' => '🔄 Обновить',
                'callback_data' => '/room_list'
            ]),
        ]);

        return $keyboard;
    }

    /**
     * @param int $chatId
     * @return Keyboard
     */
    private function createBookingsKeyboard(int $chatId): Keyboard
    {
        $bookings = Booking::query()
            ->whereHas('user', function ($query) use ($chatId) {
                $query->where('telegram_chat_id', $chatId);
            })
            ->where('status', 'accepted')
            ->whereToday('start_at')
            ->get();

        $keyboard = Keyboard::make()->inline();

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $mainButton  = Keyboard::inlineButton([
                'text' => "🏢 {$booking->room->title}"
                    . " 🕒 {$booking->start_at->format('H:i')}"
                    . "- {$booking->end_at->format('H:i')}",
                'callback_data' => "/room_detail_{$booking->room->id}"
            ]);

            $actionButton = match (true) {
                $booking->start_at > now() && $booking->end_at > now() => Keyboard::inlineButton([
                    'text' => '❌ Отменить',
                    'callback_data' => "/cancel_booking_$booking->id"
                ]),
                $booking->start_at < now() && $booking->end_at > now() => Keyboard::inlineButton([
                    'text' => '🆙 Продлить',
                    'callback_data' => "/booking_times_{$booking->room->id}"
                ]),
                default => Keyboard::inlineButton([
                    'text' => '🔚 Завершена',
                    'callback_data' => "/room_detail_{$booking->room->id}"
                ]),
            };

            $keyboard->row([$mainButton, $actionButton]);
        }

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '➕ Новая бронь',
                'callback_data' => '/room_list'
            ]),
            Keyboard::inlineButton([
                'text' => '🔄 Обновить',
                'callback_data' => '/my_bookings'
            ])
        ]);

        return $keyboard;
    }

    /**
     * @param Room $room
     * @return Keyboard
     */
    private function createRoomDetailKeyboard(Room $room): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '🕒 Забронировать время',
                'callback_data' => "/booking_times_$room->id"
            ])
        ]);

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '📅 Мои бронирования',
                'callback_data' => '/my_bookings'
            ])
        ]);

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '⬅️ Назад к списку',
                'callback_data' => '/room_list'
            ])
        ]);

        return $keyboard;
    }

    /**
     * Генерирует хеш для сообщения, учитывая текст и клавиатуру
     *
     * @param array $message Массив параметров сообщения
     * @return string Уникальный хеш для проверки изменений
     */
    private function generateMessageHash(array $message): string
    {
        // 1. Нормализация текста
        $text = $message['text'] ?? '';

        // Удаляем HTML-теги и лишние пробелы
        $cleanText = trim(strip_tags($text));

        // 2. Нормализация клавиатуры
        $keyboard = $message['reply_markup'] ?? [];

        // Если это объект Keyboard - преобразуем в массив
        if ($keyboard instanceof Keyboard) {
            $keyboard = $keyboard->toArray();
        }

        // 3. Подготовка данных для хеширования
        $hashData = [
            'text' => $cleanText,
            'keyboard' => $keyboard,
            'parse_mode' => $message['parse_mode'] ?? null
        ];

        // 4. Генерация стабильного JSON
        $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $jsonString = json_encode($hashData, $jsonFlags);

        // 5. Создание хеша
        return md5($jsonString);
    }
}
