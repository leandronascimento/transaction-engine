## Transaction Engine
 
#### Technologies used:
- Lumen Framework 8.0
- Nginx
- Docker
- PHPUnit
#### Features:
- Make transactions between users

### Installation
#### Clone repository

``` bash
$ git clone git@github.com:leandronascimento/transaction-engine.git transaction-engine
$ cd transaction-engine
```

#### Start Docker containers
``` bash
$ docker-compose up -d
```

#### Copy env file
``` bash
$ cp .env.example .env
```

#### Install compose dependency
``` bash
$ docker-compose exec php composer install
```

#### Run migrations
``` bash
$ docker-compose exec php php artisan migrate
```

#### Run seed
``` bash
$ docker-compose exec php php artisan db:seed
```

#### Run tests
``` bash
$ docker-compose exec php ./vendor/bin/phpunit
```

#### HTTP
- `GET http://localhost/api/transaction`

#### API
##### Make transaction
``` bash
$ curl --location --request POST 'http://localhost/api/transaction'
```

Response:
```json
{
    
}
```
