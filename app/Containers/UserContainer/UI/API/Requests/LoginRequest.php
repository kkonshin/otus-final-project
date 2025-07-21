<?php

namespace App\Containers\UserContainer\UI\API\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
            ],
            'password' => [
                'required',
                'string',
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
            'password' => 'Пароль',
        ];
    }
}
