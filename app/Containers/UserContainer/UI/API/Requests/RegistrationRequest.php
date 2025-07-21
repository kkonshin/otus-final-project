<?php

namespace App\Containers\UserContainer\UI\API\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{

    /**
     * Разрешить выполнение запроса (true = без ограничений)
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила проверки применимые к запросу
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'unique:users',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
            ],
        ];
    }

    /**
     * Сообщения об ошибках
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'required' => 'Введите :attribute',
            'max' => 'Превышена длинна :attribute',
            'confirmed' => 'Данные не совпадают :attribute',
            'unique' => 'Такой E-mail уже зарегистрирован',
            'email.email' => 'Не корректный :attribute',
        ];
    }

    /**
     * Пользовательские атрибуты для вывода ошибок валидации
     *
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'email' => 'E-mail адрес',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'password' => 'Пароль',
        ];
    }
}
