[![Travis CI](http://img.shields.io/travis/TeenQuotes/website/v3.0.svg?style=flat)](https://travis-ci.org/TeenQuotes/website)
[![Software License](http://img.shields.io/badge/License-CC%20BY--NC--SA-orange.svg?style=flat)](https://github.com/TeenQuotes/website/blob/v3.0/LICENSE.md)
[![API version](http://img.shields.io/badge/API%20version-1.0alpha-blue.svg?style=flat)](https://github.com/TeenQuotes/api-documentation)

# What is it?
This is the source code for the next release of Teen Quotes, using the awesome PHP framework Laravel.

# What is Teen Quotes?
Teen Quotes lets teenagers share their daily thoughts and feelings. It describes their daily life and feelings in a few words that they can share with their friends.

# Basic dependencies
- PHP >= 5.4 with the PDO and MCrypt extensions
- MySQL >= 5.6 (because we need to perform FULLTEXT search on InnoDB tables)
- Memcached >= 1.4 or another cache storage (Redis for example)

# How to run locally?
Add the following configuration files from the [Laravel framework](https://github.com/laravel/laravel):
- `app/config/database.php`
- `app/config/mail.php`
- `app/config/services.php`

And then run:

    $ composer install
    $ php artisan migrate
    $ php artisan db:seed
    $ php artisan serve

## Editing the front-end
If you want to edit JS files and Compass files, you will need to install [Compass](http://compass-style.org/install/), [node.js](http://nodejs.org/) and some node.js packages. You can install them by running:

	$ npm install

And then take advantage of the Gulpfile:

	$ gulp

## Who did this?
**Antoine AUGUSTI** - http://www.antoine-augusti.fr

## Under which license?
CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/4.0/