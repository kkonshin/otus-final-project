<?php

namespace App\Containers\TelegramContainer\UI\CLI\Commands;

use App\Containers\TelegramContainer\Exceptions\SetTelegramWebhookException;
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
     * @return void
     */
    public function handle(): void
    {
        try {
            $url = $this->argument('url') ?? config('telegram.bots.bindroom_bot.webhook_url');

            if (empty($url)) {
                throw new SetTelegramWebhookException('Webhook URL for Telegram not specified', 500);
            }

            $response = Telegram::setWebhook([
                'url' => $url,
            ]);

            if ($response !== true) {
                throw new SetTelegramWebhookException('Telegram API returned false');
            }

            Log::info("Set Telegram Webhook successfully", [
                'url' => $url,
            ]);

        } catch (Throwable $e) {
            report($e);
            $this->error("Command failed: " . $e->getMessage());
        }
    }
}
