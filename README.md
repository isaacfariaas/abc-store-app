# ABC Store API

## Getting Started 

### Requirements

- Docker ^20.10
- Docker-Compose ^2.12

### Notes

### Documentation

- The documentation is avaliable in localhost:5000

### Setup environment

Copy default environment variables

```sh
 cp .env.example .env
```
Docker compose up:

```sh
 docker compose up -d --build
```
Composer Install:

```sh
 docker compose exec app composer install
```
Docker compose down:

```sh
 docker compose down -v
```

Sail alias:

```sh
 alias sail=./vendor/bin/sail
```

Sail up:

```sh
 sail up -d --build
```

Generate application Key:

```sh
 sail artisan key:generate
```

Database migration:

```sh
 sail artisan migrate:fresh --seed
```

Run Tests:

```sh
 sail test
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
