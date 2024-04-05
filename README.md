
## Setup ENV

To change the default portsfor running the docker containers, 
ser .env:

```
API_PORT=80
FE_PORT=8080
```

## Spin up API

```
docker-compose up -d
docker-compose exec php-fpm composer install
```

## Test the backend is working

[http://localhost](http://localhost)

## Frontend

[http://localhost:8080](http://localhost:8080)

