up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

restart: down up

build:
	docker-compose build

console-in:
	docker-compose run app bash

composer-install:
	docker-compose run app composer install

migration:
	docker-compose run app php bin/console d:m:m --no-interaction

new-migration:
	docker-compose run app php bin/console doctrine:migrations:diff

cron:
	docker-compose run app php bin/console cli:planka

ps:
	docker-compose ps

log:
	docker-compose logs