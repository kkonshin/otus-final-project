<?php

namespace App\Containers\BookingContainer\UI\API\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
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
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')],
            'start_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $diffInMinutes = now()->addHours(2)->diffInMinutes($value);

                    if ($diffInMinutes <= 119.9) {
                        $fail("Дата должна быть от 2-х часов от нынешнего времени");
                    }
                }],
            'end_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $diffInMinutes = Carbon::create($this->input('start_at'))->diffInMinutes($value);

                    $this->input('start_at');
                    if ($diffInMinutes <= 59.9) {
                        $fail("Дата должна быть от 2-х часов от нынешнего времени");
                    }
                }],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'user_id' => 'Неверно указан :attribute',
            'room_id' => 'Неверно указан :attribute',
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
            'room_id' => 'ID комнаты',
            'start_at' => 'Начало бронирования',
            'end_at' => 'Окончание бронирования',
        ];
    }
}
