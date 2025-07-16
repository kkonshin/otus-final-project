<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\MoonShine\Resources;

use App\Containers\UserContainer\Models\User;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

#[Icon('user-circle')]
#[Group('Приложение', 'folder', translatable: true)]
#[Order(1)]
/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $column = 'email';

    protected string $group = 'main';

    protected string $route = 'users';

    protected string $table = 'users';

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    protected string $title = 'Пользователи';

    public function getTitle(): string
    {
        return __('ui.resource.user_title');
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Имя', 'first_name')
                ->nullable(),

            Text::make('Фамилия', 'last_name')
                ->sortable()
                ->nullable(),

            Email::make('E-mail', 'email')
                ->sortable(),

            Date::make('Дата создания', 'created_at')
                ->format("Y-m-d H:i:s")
                ->sortable(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),

                        Flex::make([
                            Text::make('Имя', 'first_name')
                                ->nullable(),

                            Text::make('Фамилия', 'last_name')
                                ->nullable(),
                        ]),

                        Email::make('E-mail', 'email')
                            ->required(),
                    ])->icon('information-circle'),

                    Tab::make('Пароль', [
                        Collapse::make('Установить пароль', [
                            Password::make('Пароль', 'password')
                                ->required()
                                ->customAttributes(['autocomplete' => 'new-password'])
                                ->eye(),

                            PasswordRepeat::make('Повторите пароль', 'password_repeat')
                                ->required()
                                ->customAttributes(['autocomplete' => 'confirm-password'])
                                ->eye(),
                        ])->icon('lock-closed'),
                    ])->icon('lock-closed'),
                ]),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'first_name' => 'nullable',
            'email' => [
                'sometimes',
                'bail',
                'required',
                'email',
                Rule::unique('users')->ignoreModel($item),
            ],
            'password' => $item->exists
                ? 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat'
                : 'required|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'email',
            'last_name',
        ];
    }
}
