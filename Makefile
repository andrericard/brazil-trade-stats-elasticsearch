include ./src/.env
PHP = ${COMPOSE_PHP_CONTAINER_NAME}

install:
	@docker exec -u 1000 $(PHP) composer install
	@docker exec $(PHP) php artisan key:generate

up:
	@docker-compose -f ./src/docker-compose.yml up --build -d

down:
	@docker-compose -f ./src/docker-compose.yml down

down-volumes:
	@docker-compose -f ./src/docker-compose.yml down -v

restart:
	@docker-compose -f ./src/docker-compose.yml restart

reset: down-volumes up

permissions:
	@docker exec $(PHP) chown -cR 1000:33 ./bootstrap/cache/ ./storage/
	@docker exec $(PHP) chmod -cR 775 ./bootstrap/cache/ ./storage/

clear:
	@docker exec $(PHP) php artisan optimize:clear

php:
	@docker exec -it -u 1000 $(PHP) bash

tinker:
	@docker exec -it $(PHP) bash -c "while true; do php artisan tinker; done"
