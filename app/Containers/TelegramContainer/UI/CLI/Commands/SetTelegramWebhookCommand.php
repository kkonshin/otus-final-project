<?php

namespace App\Containers\TelegramContainer\UI\CLI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class SetTelegramWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Задать webhook для Telegram';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        try {
            $url = $this->argument('url') ?? config('telegram.bots.bindroom_bot.webhook_url');

            if (empty($url)) {
                $this->error('Webhook URL for Telegram not specified');
                return 1;
            }

            $response = Telegram::setWebhook([
                'url' => $url,
            ]);

            if ($response !== true) {
                $this->error('Telegram API returned false');
                return 1;
            }

            Log::info("Set Telegram Webhook successfully", [
                'url' => $url,
            ]);
        } catch (Throwable $e) {
            report($e);
            $this->error("Command failed: " . $e->getMessage());
        }

        return 0;
    }
}
