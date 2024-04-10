# LW Servers

This app reads an .xlsx file containing a list of server information and serves it over an API endpoint.

The frontend app will allow you to filter, order and navigate the result pages.

# Spin the App

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

## Open the frontend
[http://localhost:8080](http://localhost:8080)


## Frontend Dev container

This container is used only for node/npm development and it is **not** started by default.

To spin the container:

```
docker-compose up -d vue
```
You can then run the dev server: 

```
docker-compose exec vue npm i
docker-compose exec vue npm run dev
```

And go to [http://localhost:5173/](http://localhost:5173/)

### Build the frontend

Build the project dist available through [http://localhost:8080](http://localhost:8080):

```
docker-compose exec vue npm run build
```