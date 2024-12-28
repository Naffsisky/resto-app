# Install

Install Docker & Docker Compose

## Buat file .env

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

## Docker Compose

`docker-compose up -d`

### Exec Container

```
docker exec -it resto-api composer install
docker exec -it resto-api php artisan key:generate
docker exec -it resto-api php artisan storage:link
docker exec -it resto-api php artisan migrate
docker exec -it resto-api php artisan db:seed
```

<img src="https://data-collection.s3.nevaobjects.id/logo.png" style="width: 100px; height: 100px;" />
