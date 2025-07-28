<?php

namespace Tests\Feature;

use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    /**
     * @return void
     */
    public function test_webhook(): void {
        $this
            ->artisan('telegram:webhook')
            ->assertOk();
    }

    /**
     * @return void
     */
    public function test_notify(): void {
        $this
            ->artisan('telegram:notify')
            ->assertOk();
    }

    /**
     * @return void
     */
    public function test_webhook_wrong_url(): void {
        $this
            ->artisan('telegram:webhook wrong-url')
            ->assertFailed();
    }
}
