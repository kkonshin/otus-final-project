## Build guid

`docker-compose up -d`

`docker-compose run --rm composer install`

`docker-compose exec app php artisan key:generate`

`docker-compose exec app php artisan migrate`

`
docker-compose exec app chown -R www-data:www-data /var/www/public
`

`
docker-compose exec app chmod -R 777 /var/www/storage
`

## Пересобрать контейнер
`docker-compose down
docker-compose build app
docker-compose up -d`

## Проверка расширения редис
`docker-compose exec bindroom-app php -m | grep redis`
