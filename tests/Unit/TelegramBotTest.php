<?php

namespace Tests\Unit;

use App\Containers\TelegramContainer\Enums\CallbackCommand;
use PHPUnit\Framework\TestCase;


class TelegramBotTest extends TestCase
{
    public function test_callback_command_detect(): void {
        $callbackCommand = CallbackCommand::detect('/cancel_booking_');
        $this->assertInstanceOf(CallbackCommand::class, $callbackCommand);
    }

    public function test_callback_command_detect_wrong(): void {
        $callbackCommand = CallbackCommand::detect('/wrong');
        $this->assertEmpty($callbackCommand);
    }

    public function test_callback_extract_params(): void {
        $data = '/rooms_page_';

        $callbackCommand = CallbackCommand::detect($data);
        $result = $callbackCommand->extractParams($data);

        $this->assertEquals(["page" => 0], $result);
    }
}
