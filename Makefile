.PHONY: setup up down restart build test shell migrate seed

setup:
	@echo "Setting up the environment..."
	cp .env.example .env || true
	docker-compose build
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate --seed
	@echo "Setup complete! App running at http://localhost:8000"

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

build:
	docker-compose build

test:
	docker-compose exec app php artisan test

shell:
	docker-compose exec app sh

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed
