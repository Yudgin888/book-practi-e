up: docker-up
stop: docker-stop
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-up
test: book-test
perm: book-permissions
cache: book-cache

docker-up:
	docker-compose up -d

docker-stop:
	docker-compose stop

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

book-test:
	docker exec -it book-php-cli php bin/phpunit

book-cache:
	docker exec -it book-php-cli php bin/console cache:clear

book-permissions:
	sudo chown -R www-data:www-data public_html/
	sudo chmod -R g+w public_html/

routes:
	docker exec -it book-php-cli php bin/console debug:router
