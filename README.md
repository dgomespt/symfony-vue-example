
## Setup ENV

To change the default ports for running the docker containers, 
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


## Frontend Dev container

This container is used only for node/npm development and it is **not** started by default.

To spin the container:

```
docker-compose up -d vue
```
You can then run the dev server: 

```
docker-compose exec vue npm run dev
```

Or build the project to expose it in [http://localhost:8080](http://localhost:8080)

```
docker-compose exec vue npm run build
```