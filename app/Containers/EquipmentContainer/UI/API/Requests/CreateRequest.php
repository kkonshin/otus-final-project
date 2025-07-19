<?php

namespace App\Containers\EquipmentContainer\UI\API\Requests;

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
            'title' => ['required', 'string',   Rule::unique('equipments', 'title')],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'title' => 'Не корректно указано :attribute',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'title' => 'Наименование оборудования',
        ];
    }
}
