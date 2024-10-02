In this project used following versions:
- php v8.1.29
- Laravel Framework 10.48.22
- mySQL 9.0.1

To run it first you need to download the source. You can use:
- git clone https://github.com/rubilnik89/draivi.git

After that go to project directory and run:
- composer install

It allows to install all framework files and related libraries that were used in the project

After that you need to modify .env file and add your DB connection variables:
- DB_CONNECTION=
- DB_HOST=
- DB_PORT=
- DB_DATABASE=
- DB_USERNAME=
- DB_PASSWORD=

After you added your DB settings you need to run migrations to create schema:

- php artisan migrate

After migrations are done you need to run a server f.e.
- php artisan serve

Then you can go to browser and open page:
- http://127.0.0.1:8000

To run a parser you can run command:
- php artisan app:fetch-prices

OR you can push Update prices button in the right part.
Delete prices button clears all table. (It's made for testing purposes)

