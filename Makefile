# Полная сборка приложения
setup: env install-dep key-gen sql-prep build-frontend

# Установить зависимости
install-dep:
	yarn install
	composer install --ignore-platform-req=php

# Создание объектов бд
sql-prep:
	php artisan migrate
	php artisan migrate --path=database/migrations/functions
	php artisan migrate --path=database/migrations/triggers

# Собрать фронт
build-frontend:
	yarn run prod

# Сгенерировать ключ
key-gen:
	php artisan key:generate

# Копирование переменных окружения в новый файл
env:
	cp -n .env.example .env

autoload:
	composer dumpautoload
