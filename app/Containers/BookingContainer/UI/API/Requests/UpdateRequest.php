<?php

namespace App\Containers\BookingContainer\UI\API\Requests;

use App\Containers\BookingContainer\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists('bookings', 'id')],
            'user_id' => ['integer', Rule::exists('users', 'id')],
            'room_id' => ['integer', Rule::exists('rooms', 'id')],
            'status' => [Rule::in(Status::values())],
            'start_at' => ['required_with:end_at', 'date',  Rule::date()->after(now()->addDay())],
            'end_at' => ['required_with:start_at', 'date', 'after:start_at'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'id' => 'Неверно указан :attribute',
            'user_id' => 'Неверно указан :attribute',
            'room_id' => 'Неверно указан :attribute',
            'status' => 'Указан неверный :attribute',
            'start_at' => 'Не корректная дата :attribute',
            'end_at' => 'Не корректная дата :attribute',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'id' => 'Идентификатор',
            'user_id' => 'ID пользователь',
            'room_id' => 'ID комнаты',
            'status' => 'Статус бронирования',
            'start_at' => 'Начало бронирования',
            'end_at' => 'Окончание бронирования',
        ];
    }
}
