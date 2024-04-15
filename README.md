![example workflow](https://github.com/df3g/lw-servers/actions/workflows/ci.yml/badge.svg)

# LW Servers

This app reads an .xlsx file containing a list of server information and serves it over an API endpoint.

The frontend app will allow you to filter, order and navigate the result pages.

## Implementation notes:

### Loading data into the app:

Since no database is required, I opted to load the file using `phpoffice/phpspreadsheet`, converting each row into a `Server` object and save the list in cache (redis).

For the sake of brevity, I opted not to include a command to import the file contents and validate each row. 

However, some effort was done to make sure that the data is extracted in a way that each field can be ordered and filtered correctly.

    For instance, the HDD string `2x500TBSATA2` is split into parts:

    - No of disks
    - Type (SATA, SSD, etc.)
    - Disk capacity
    - Total capacity is computed in GB (i.e 1.000.000)

### Serving the data
The server list can then be accessed at ` GET /servers`

The following query string params are supported (all optional):

|Param|Default|Possible values|
|---|---|---|
|page|1|positive number|
|itemsPerPage|50|positive number|
|order[model]||asc, desc|
|order[ram]||asc, desc|
|order[hdd]||asc, desc|
|order[location]||asc, desc|
|order[price]||asc, desc|
|filters[hddType]||any string|
|filters[ram]||any string|
|filters[storage]||any string|
|filters[location]||any string|

### Interacting with the data

A Vue 3 app is included with the project.
It allows you to:
- Apply filters
- Toggle column order
- Navigate through pages
- Pick two servers with differences highlighted

All operations on the table data are done in the backend, except for comparing servers.

The app is focused on function and some minor styling is applied with the aid of Tailwind.

# Spin the App

```
docker-compose up -d
docker-compose exec php-fpm composer install
```
Go to [http://localhost:8080](http://localhost:8080) to access the frontend.


# Development env:

### Backend

To enter the php-fpm container

`docker-compose exec php-fpm /bin/bash`

Useful commands:

```
composer install #install composer dependencies
./bin/phpunit #run unit tests
./bin/console cache:pool:clear server.cache #clear contents of cached servers
```

### Frontend Dev container

For the purpose of running development tasks I included a node/npm container that is **not** started by default.

If you need to spin the container:

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

After building, it is recommended that you restart the webserver container

## Unit tests

There are some unit and integration tests included.

Run the unit test suite with:
``` 
docker-compose exec php-fpm ./bin/phpunit
```
