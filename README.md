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

You can also run `php artisan october:down` to completely remove the database tables.

## Running the site

* Run `php artisan serve` to start the development server
* Run `gulp` to build any JS and CSS files
* You can log into the admin by navigating to **/admin** and using **admin:admin** as the **username:password**.

## Contributing

The main bulk of the code for the site is in the **plugins** folder. This is where all third party plugins are and also the custom **harassmap** plugins.

The **theme** folder also contains the main pages of the application. This is where the HTML/CSS of the site is and the HTML is written in [twig](https://twig.symfony.com/) which allows php code to be used inside the HTML.
