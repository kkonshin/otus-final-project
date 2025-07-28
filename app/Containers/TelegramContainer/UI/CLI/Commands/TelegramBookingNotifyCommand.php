<?php

namespace App\Containers\TelegramContainer\UI\CLI\Commands;

use App\Containers\BookingContainer\Models\Booking;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class TelegramBookingNotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Рассылка напоминаний о бронировании в Telegram';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            Booking::query()
                ->whereHas('user', function ($query) {
                    $query->whereNotNull('telegram_chat_id');
                })
                ->where('start_at', '>', now())
                ->where('start_at', '<=', now()->addMinutes(30))
                ->chunk(100, function($bookings) {
                    $bookings->each(function($booking) {
                        $minutesLeft = round(now()->diffInMinutes($booking->start_at));

                        if ($minutesLeft <= 0) {
                            return;
                        }

                        $text = "Напоминание: бронирование в {$booking->start_at->format('H:i')}"
                            . " (через $minutesLeft мин.)";

                        Telegram::sendMessage([
                            'chat_id' => $booking->user->telegram_chat_id,
                            'text' => $text,
                        ]);
                    });
                });
        } catch (Throwable $e) {
            report($e);
            $this->error("Command failed: " . $e->getMessage());
        }

        return 0;
    }
}
