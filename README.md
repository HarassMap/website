# HarassMap

## Prerequisites

* [Composer](https://getcomposer.org/doc/00-intro.md)
* [NodeJs](https://nodejs.org/en/download/)

## Setting up the site

* `composer install` to install php dependencies.
* `npm install` to install JS dependencies

Set up a local database:

* Database Name: **harassmap**
* Database User: **harassmap**
* Database Pass: **harassmap**

Run `php artisan october:up` to build the database tables.
Run `php artisan harassmap:up` to seed the tables with data needed to run the site.

## Running the site

* Run `php artisan serve` to start the development server
* Run `gulp` to build any JS and CSS files