<?php

declare(strict_types=1);

namespace App\Containers\RoomBookingContainer\UI\MoonShine\Resources;

use App\Containers\RoomBookingContainer\Models\Room;
use Illuminate\Support\Carbon;
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
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

#[Icon('building-office-2')]
#[Group('Приложение', 'folder', translatable: true)]
#[Order(2)]
/**
 * @extends ModelResource<Room>
 */
class RoomResource extends ModelResource
{
    protected string $model = Room::class;

    protected string $column = 'title';

    protected string $group = 'main';

    protected string $route = 'rooms';

    protected string $table = 'rooms';

    protected bool $simplePaginate = true;

    protected bool $columnSelection = true;

    protected string $title = 'Комнаты';

    public function getTitle(): string
    {
        return 'Комнаты';
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Наименование', 'title')
                ->sortable()
                ->nullable(),

            Number::make('Вместимость', 'capacity')
                ->required()
                ->sortable(),

            Text::make('Описание', 'description')
                ->nullable(),

            Number::make('Этаж', 'floor')
                ->sortable()
                ->required(),

            Date::make('Время открытия', 'available_from')
                ->withTime()
                ->format("H:i")
                ->sortable(),

            Date::make('Время закрытия', 'available_to')
                ->withTime()
                ->format("H:i")
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
                            Text::make('Наименование', 'title')
                                ->nullable(),

                            Number::make('Вместимость', 'capacity')
                                ->required()
                                ->sortable(),

                            Number::make('Этаж', 'floor')
                                ->required()
                                ->sortable(),

                            Textarea::make('Описание', 'description')
                                ->nullable(),
                        ]),

                        Date::make('Дата создания', 'created_at')
                            ->withTime()
                            ->format("Y-m-d H:i:s")
                            ->default(now()->toDateTimeString()),
                    ])->icon('user-circle'),

                    Tab::make('Расписание', [
                        Collapse::make('Установить Расписание', [
                            Date::make('Время открытия', 'available_from')
                                ->withTime()
                                ->format("H:i"),

                            Date::make('Время закрытия', 'available_to')
                                ->withTime()
                                ->format("H:i"),
                        ])->icon('clock'),
                    ])->icon('clock'),
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
     * @param Room $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'floor' => 'required|integer',
            'available_from' => 'date|nullable',
            'available_to' => 'date|nullable',
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
        ];
    }

    protected function beforeCreating(mixed $item): mixed
    {
        return $this->mergeDateTime($item);
    }

    protected function beforeUpdating(mixed $item): mixed
    {
        return $this->mergeDateTime($item);
    }

    protected function mergeDateTime(mixed $item): mixed
    {
        $requestData = request()->toArray();

        if (!empty($requestData['available_from'])) {
            $mergeData['available_from'] = Carbon::make(
                $requestData['available_from']
            )->format('H:i:s');
        };

        if (!empty($requestData['available_to'])) {
            $mergeData['available_to'] = Carbon::make($requestData['available_to'])->format('H:i:s');
        };

        if (!empty($mergeData)) {
            request()->merge($mergeData);
        }

        return $item;
    }
}
