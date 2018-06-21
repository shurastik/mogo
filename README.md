mogo
====

composer will prompt for MySQL connection details

`cd /path/to/project/`

`composer -o install`

`bin/console doctrine:schema:update --force`

`bin/console doctrine:fixtures:load -n`

`bin/console server:run`

project can be accessible at http://127.0.0.1:8000/

run tests:

`vendor/bin/phpunit`
