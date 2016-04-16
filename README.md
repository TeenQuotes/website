[![Travis CI](http://img.shields.io/travis/TeenQuotes/website/v3.0.svg?style=flat)](https://travis-ci.org/TeenQuotes/website)
[![Software License](http://img.shields.io/badge/License-CC%20BY--NC--SA-orange.svg?style=flat)](https://github.com/TeenQuotes/website/blob/v3.0/LICENSE.md)
[![API version](http://img.shields.io/badge/API%20version-1.0alpha-blue.svg?style=flat)](https://github.com/TeenQuotes/api-documentation)

# What is it?
This is the source code for the next release of Teen Quotes, using the awesome PHP framework Laravel.

# What is Teen Quotes?
Teen Quotes lets teenagers share their daily thoughts and feelings. It describes their daily life and feelings in a few words that they can share with their friends.

# Basic dependencies
- PHP >= 5.5 with the PDO and MCrypt extensions
- MySQL >= 5.6 (because we need to perform FULLTEXT search on InnoDB tables)
- Redis server for cache and queues

# External services I'm relying on
- [Mailgun](http://www.mailgun.com) for transactional emails
- [Mailchimp](http://mailchimp.com) for newsletters
- [Easyrec](http://easyrec.org) for the recommendation system (not actively used for the moment)
- [Pushbullet](https://www.pushbullet.com) for notifications for administrators
- [Bugsnag](https://bugsnag.com) to track exceptions

# How to run locally?
Update environment variables with your values in the file `.env.example` and then rename the file to `.env`.

Install packages, seed the datbase and run the local server with these commands:

```bash
$ composer install
$ php artisan migrate
$ php artisan db:seed
$ php artisan serve
```

## Editing the front-end
If you want to edit JS files and Compass files, you will need to install [Compass](http://compass-style.org/install/), [node.js](http://nodejs.org/) and some node.js packages. You can install them by running the following commands from the root directory:
```bash
# Install Gulp globally
$ sudo npm install -g gulp
# Grab required packages
$ npm install
```

And then take advantage of the Gulpfile:
```bash
$ gulp
```

## Who did this?
**Antoine AUGUSTI** - http://www.antoine-augusti.fr

## Under which license?
CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/4.0/
