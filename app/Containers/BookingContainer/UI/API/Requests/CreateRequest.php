<?php

namespace App\Containers\BookingContainer\UI\API\Requests;

use App\Containers\BookingContainer\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
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
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'status' => ['required', Rule::in(Status::values())],
            'start_at' => ['required', 'date',  Rule::date()->after(now()->addDay())],
            'end_at' => ['required', 'date', 'after:start_at'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'user_id' => 'Введите :attribute',
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
            'user_id' => 'ID пользователь',
            'status' => 'Статус бронирования',
            'start_at' => 'Начало бронирования',
            'end_at' => 'Окончание бронирования',
        ];
    }
}
