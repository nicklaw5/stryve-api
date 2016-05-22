# stryve-api-php
A [Laravel](https://laravel.com/) API server for handling requests made by [Stryve](https://github.com/nicklaw5/stryve).

## Usage
Please see the [Gettting Started](https://github.com/nicklaw5/stryve/wiki/Getting-Started) tutorial for full installation instructions.
```bash
$ git clone https://github.com/nicklaw5/stryve-api-php.git  # clone the repo
$ composer install                                          # intsall package dependencies
$ mv .env-example .env                                      # re-name environement config file
$ php artisan key:generate                                  # generate a new application key
$ php artisan migrate:refresh --seed                        # migrate and seed database
$ php artisan serve --port=8181                             # run the server on http://localhost:8181/
```

## Getting Started
The getting started tutorial can be here - https://github.com/nicklaw5/stryve/wiki/Getting-Started

## Issues and PR's
Stryve is under development. Issues and pull requets are vey welcome. Please [report any problems here](https://github.com/nicklaw5/stryve//issues).