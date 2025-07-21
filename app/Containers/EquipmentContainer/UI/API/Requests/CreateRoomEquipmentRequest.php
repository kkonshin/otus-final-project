<?php

namespace App\Containers\EquipmentContainer\UI\API\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoomEquipmentRequest extends FormRequest
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
            'equipment_id' => ['required', 'integer', Rule::exists('equipments', 'id')],
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')],
            'quantity' => ['integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'equipment_id' => 'Неверно указан :attribute',
            'room_id' => 'Неверно указан :attribute',
            'quantity' => 'Не корректно указано :attribute',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'equipment_id' => 'ID оборудования',
            'room_id' => 'ID комнаты',
            'quantity' => 'Количество',
        ];
    }
}
