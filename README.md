Application test for mindgeek
## The Task
- Using PHP No framework
- Latest version of php
- Latest version of PHPUnit For Unit Test

## Run the project Using Docker
- `docker-compose up -d`
- access using any browser http://localhost

## Tests
- `docker-compose exec php vendor/bin/phpunit`
##Run the project Not using docker
Clone  repository [my-test](https://github.com/marolahy/my-test  the your httpd server root folder
```
git clone https://github.com/marolahy/my-test
cd my-test/app
composer update
composer dumpautoload -o
vendor/bin/phpunit
```
## Application URL
[my-test](http://127.0.0.1/my-test/public)

## Samples
You can find samples valid file in [samples](https://github.com/marolahy/my-test/tree/master/app/Tests/SchoolBoard/_files)
