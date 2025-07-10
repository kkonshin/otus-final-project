<?php

use App\Containers\BookingContainer\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        // TODO: Как будет созданы комнаты добавить room_id
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
//            $table->unsignedBigInteger('room_id');
            $table->enum('status', Status::values())->default(Status::WAITING_CONFIRMATION->value);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
//            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
