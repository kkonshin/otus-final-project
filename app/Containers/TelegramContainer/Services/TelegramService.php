<?php

namespace App\Containers\TelegramContainer\Services;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\UserContainer\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class TelegramService
{
    private const int ITEMS_PER_PAGE = 5;
    private const string CACHE_PREFIX = 'tg_last_msg_';
    private const int CACHE_TIME = 60;

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

        //TODO: Получить пользователя из БД по Telegram chatId ($userId)
        /** @var User $user */
        $user = User::query()->firstOrFail();

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
     * Общий метод для отправки или редактирования сообщения
     *
     * @param $chatId
     * @param array $message
     * @param int|null $editMessageId
     * @return void
     * @throws TelegramSDKException
     */
    private function sendOrEditMessage($chatId, array $message, ?int $editMessageId = null): void
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
                Cache::put(self::CACHE_PREFIX."_{$chatId}_$sentMessage->messageId", $currentHash, self::CACHE_TIME);
            }
        } catch (Throwable $e) {
            if (!str_contains($e->getMessage(), 'message is not modified')) {
                report($e);
                $sentMessage = Telegram::sendMessage($message);
                Cache::put(self::CACHE_PREFIX."_{$chatId}_$sentMessage->messageId", $currentHash, self::CACHE_TIME);
            }
        }
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
    protected function generateMessageHash(array $message): string
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
