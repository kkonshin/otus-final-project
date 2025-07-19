<?php

namespace App\Containers\EquipmentContainer\UI\API\Requests;

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
            'id' => ['required', 'integer', Rule::exists('equipments', 'id')],
            'title' => ['string',   Rule::unique('equipments', 'title')],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'id' => 'Неверно указан :attribute',
            'title' => 'Не корректно указано :attribute',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'id' => 'Идентификатор',
            'title' => 'Наименование оборудования',
        ];
    }
}
