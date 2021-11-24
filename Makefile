tests:
	php bin/console doctrine:fixtures:load --env=test -n
	php bin/phpunit

make-migration:
	php bin/console make:migration

migrate-migration:
	php bin/console doctrine:migrations:migrate
	APP_ENV=test php bin/console doctrine:migrations:migrate

migrate-migration-win:
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:migrations:migrate --env=test -n

load-fixtures:
	php bin/console doctrine:fixtures:load -n
	APP_ENV=test php bin/console doctrine:fixtures:load -n

load-fixtures-win:
	php bin/console doctrine:fixtures:load -n
	php bin/console doctrine:fixtures:load --env=test -n

install-frontend:
	npm install

build-frontend:
	npm run dev

server-start:
	php -S localhost:8000 -t public/