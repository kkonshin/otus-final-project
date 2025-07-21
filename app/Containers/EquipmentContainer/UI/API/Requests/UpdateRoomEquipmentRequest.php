<?php

namespace App\Containers\EquipmentContainer\UI\API\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomEquipmentRequest extends FormRequest
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
            'id' => ['required', 'integer', Rule::exists('room_equipment', 'id')],
            'equipment_id' => ['integer', Rule::exists('equipments', 'id')],
            'room_id' => ['integer', Rule::exists('rooms', 'id')],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'id' => 'Неверно указан :attribute',
            'equipment_id' => 'Неверно указан :attribute',
            'room_id' => 'Неверно указан :attribute',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'id' => 'Идентификатор',
            'equipment_id' => 'ID оборудования',
            'room_id' => 'ID комнаты',
        ];
    }
}
