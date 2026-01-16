.PHONY: setup composer env key docker

# -----------------------
# Разворачивание проекта
# -----------------------
setup: composer env key docker
	@echo "Приложение готово к запуску!"

composer:
	@echo "Устанавливаем зависимости через Composer..."
	composer install

env:
	@echo "Копируем .env.example в .env..."
	cp -n .env.example .env || true

key:
	@echo "Генерируем ключ приложения..."
	php artisan key:generate

docker:
	@echo "Собираем и запускаем Docker контейнеры..."
	docker compose build --no-cache
	docker compose up -d
