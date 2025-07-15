# Сервис "Bindroom" бронирования переговорных комнат

## Описание проекта
#### Проект представляет собой веб-приложение для организации бронирования переговорных комнат в офисе, включающее:

- **Клиентскую часть** для сотрудников
- **Административную панель** для управления
- **Telegram-бота** для удобного взаимодействия
- **REST API** для интеграции с другими системами

## Оглавление
1. [Основные возможности системы](#основные-возможности-системы)
2. [Технологический стек](#технологический-стек)
3. [Развертывание проекта](#Развертывание-проекта)
4. [Настройка окружения](#настройка-окружения)
5. [Работа с базой данных](#работа-с-базой-данных)
6. [Настройка админ-панели MoonShine](#настройка-админ-панели-moonshine)
7. [Команды для разработки](#команды-для-разработки)
8. [Полезные ссылки](#полезные-ссылки)

### Основные возможности системы

#### Для сотрудников:
- Просмотр доступных переговорных комнат
- Бронирование комнат на выбранные дату и время
- Управление своими бронированиями (отмена, изменение)
- Получение уведомлений о бронировании

#### Для администраторов:
- Полное управление переговорными комнатами (добавление, редактирование, удаление)
- Просмотр всех бронирований
- Управление пользователями
- Анализ статистики использования помещений

#### Особенности системы:
- Интеграция с Telegram через бота
- Поддержка повторяющихся бронирований
- Система уведомлений (Email/Telegram)
- Статистика загрузки помещений

### Технологический стек

**Backend:**
- Laravel 12 (REST API)
- PostgreSQL (основная база данных)
- Redis (кеширование и очереди)
- Laravel Sanctum (аутентификация)
- Docker и Docker Compose
- PHP 8.4
- Composer

**Интерфейсы:**
- Админ-панель: MoonShine
- Telegram Bot API
- REST API для интеграций

**Инфраструктура:**
- Docker-контейнеризация
- Оптимистичная блокировка для обработки конфликтов бронирования
- Очереди запросов для высокой нагрузки

## Развертывание проекта

1. **Клонируйте репозиторий:**
   ```bash
   git clone https://github.com/kkonshin/otus-final-project.git
   cd otus-final-project
2. **Запустите контейнеры:**
    ```bash
   docker-compose up -d --build
3. **Установите зависимости Composer:**
    ```bash
   docker-compose run --rm composer install
4. **Создайте файл окружения:**
    ```bash
   cp .env.example .env
5. **Сгенерируйте ключ приложения:**
    ```bash
    docker-compose exec app php artisan key:generate

## Настройка окружения

1. **Создайте симлинк для хранилища:**
    ```bash
    docker-compose exec app php artisan storage:link
2. **Проверьте наличие расширения Redis (если используется):**
    ```bash
    docker-compose exec app php -m | grep redis

## Работа с базой данных
1. **Выполните миграции:**
    ```bash
    docker-compose exec app php artisan migrate
   ```
   При необходимости можете перезаписать все миграции (опционально)
    ```bash
   docker-compose exec app php artisan migrate:fresh
2. **(Опционально) Заполните базу тестовыми данными:**
    ```bash
    docker-compose exec app php artisan db:seed

## Настройка админ-панели MoonShine

1. **Установите MoonShine:**
    ```bash
    docker-compose exec app php artisan moonshine:install -Q
2. **Создайте первого администратора:**
   <br><br>
   - Username(email): admin@dev.local
   - Name: Admin
   - Password: ваш_пароль
  <br><br>
3. **Создайте ресурсы (например, для пользователей):**
    ```bash
    docker-compose exec app php artisan moonshine:resource User
4. **Доступ к админ-панели:**
   <br><br>
    URL: http://127.0.0.1:8080/admin
   <br>
    Используйте учетные данные, созданные на шаге [2](#настройка-админ-панели-moonshine)
   <br><br>
5. [Официальная документация MoonShine](https://moonshine-laravel.com/ru/docs/3.x/index)

### Команды для разработки
- **Пересобрать контейнеры:**
    ```bash
    docker-compose down
    docker-compose build app
    docker-compose up -d
    ```
    ```bash
    docker-compose up -d --build
    ```
- **Тесты:**
    ```bash
    docker-compose exec app php artisan test
- **Просмотр логов:**
    ```bash
    docker-compose logs -f app

### Полезные ссылки
[Официальная документация MoonShine](https://moonshine-laravel.com/ru/docs/3.x/index)

[Laravel документация](https://laravel.com/docs/12.x)

[Docker документация](https://docs.docker.com/)
