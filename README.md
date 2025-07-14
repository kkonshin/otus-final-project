## Build guid

`docker-compose up -d`

`docker-compose run --rm composer install`

`docker-compose exec app php artisan key:generate`

`docker-compose exec app php artisan php artisan storage:link`

`docker-compose exec app php artisan migrate`

## Пересобрать контейнер
`docker-compose down
docker-compose build app
docker-compose up -d`

## Проверка расширения редис
`docker-compose exec app php -m | grep redis`
