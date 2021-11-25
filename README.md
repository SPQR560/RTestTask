1) docker-compose up -d
2) composer install
3) php bin/console doctrine:migrations:migrate
4) php bin/console doctrine:fixtures:load -n
5) npm install
6) npm run dev
7) php -S localhost:8000 -t public/

//запуск тестов
1) APP_ENV=test php bin/console doctrine:migrations:migrate
2) APP_ENV=test php bin/console doctrine:fixtures:load -n
3) php bin/phpunit