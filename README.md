
# Welcome !

NOTES-APi allows you to create “text” notes for organizations. **Honestly, it's no use**. It just allowed me to learn how to properly build APIs with the [Laravel framework](https://laravel.com/). And to understand all the necessary steps until the production.

## Features

### The data model
https://docs.google.com/drawings/d/1zmRvECCTMk26aPg_3qo65rxdvm4r8RHNzpyOgIrqTLc/edit?usp=drive_link

### Api routes
https://laravel.com/docs/11.x/controllers#api-resource-routes

### Form request
https://laravel.com/docs/11.x/validation#form-request-validation

### Policies
https://laravel.com/docs/11.x/authorization#creating-policies

The roles are:
- SUPERADMIN (can do anything)
- ADMIN (can do anything on his organization)
- USER (can create notes on his organization, modify/delete only his notes)

### api resources
https://laravel.com/docs/11.x/eloquent-resources

### Translations

You can choose the desired language for the responses by specifying **Accept-Language=xx** in the header of your request. The translated langage are **en** and **fr**.

Without language specified, the default language will be used (see you config.app file). See the middleware Middleware/DetectLocale.php

The translated message are in the /lang folder (one json file per language).

### Authentication
The tool uses sanctum.
https://laravel.com/docs/11.x/sanctum


## How to install

Clone the project 
```bash
git clone dburea01/notes-api
```

Install the dependencies

```
composer install
```

Config your .env (see the .env.example and copy it)

Run the migration files (this will create the necessary tables and fill it with some data)
```
php artisan migrate:fresh --seed
```

## Tests
All the endpoints are tested with PHPunit. 

```
php artisan test
```
## Quality code ##
### larastan ###
Larastan is installed. Allowing level 9 code quality (the highest level). https://github.com/larastan/larastan

### pint ###
Pint is also installed by default. Allowing “clean” code that is pleasant to read.


## Access to the api documentation
[scramble](https://github.com/dedoc/scramble) is installed. This tool allows you to generate high quality API docs without writing (almost) annotations. If you respect the laravel standards, then scramble will find all your routes/validation rules/responses/errors/etc...

Once the project is installed, go to http://localhost:8000/docs/api to see this documentation.

## Demo ##
https://notesapi-16ff4c1d6469.herokuapp.com/docs/api#/

### I'm learning ### 
I spent a bit of time learning though, so it's definitely not perfect. So if you have any suggestions for me, please let me know : https://github.com/dburea01/notes-api/discussions

## License
You can use it, clone, copy/paste, etc...
