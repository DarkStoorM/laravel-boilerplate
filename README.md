# Laravel - Boilerplate

Simple, a bit preconfigured boilerplate with a purpose of Learning - do **not** rely on this code for your own purposes. This was supposed to be a blog, but there was too much setup and I'm too lazy to prepare each new Laravel project. Some steps require manual work, since they depend on the environment configuration.

---

- [Laravel - Boilerplate](#laravel---boilerplate)
  - [Requirements](#requirements)
  - [Differences between a fresh Laravel install and this repo](#differences-between-a-fresh-laravel-install-and-this-repo)
    - [Preconfiguration](#preconfiguration)
    - [Additional Packages](#additional-packages)
    - [Route Separation](#route-separation)
  - [Installation](#installation)
    - [Database](#database)
  - [Testing](#testing)
    - [Browser Tests](#browser-tests)
    - [Xdebug](#xdebug)
    - [Code Coverage](#code-coverage)
    - [Test paths](#test-paths)
  - [Development](#development)
    - [Routes](#routes)
  - [Suggestions](#suggestions)
    - [VSCode](#vscode)

---

## Requirements

- PHP ^8.0
- Composer 2.0
- SQLite
- Xdebug (for Code Coverage - not really a requirement, but it's good to have)

---

## Differences between a fresh Laravel install and this repo

**This is not a professionally prepared repository** for all Laravel applications, but rather a quick pseudo-preconfiguration. Most of the *manual* tasks are not even needed, like installing Xdebug for Code Coverage, but since Laravel is perfect for TDD, having Code Coverage available is always nice.

### Preconfiguration

What actually is this "pre-configuration"? They are some simple steps **for basic applications**:

- npm dependency install
- npm audit fix
- create new `.env` files
- generate APP_KEY for regular and Dusk `.env` files
- require Dusk for --dev
- Example Feature Test is now IndexTest (I always prepare Index Tests in this place)
- create ```db.sqlite``` file
- use SQLite by default (set by ```.env```)
- prepare the following directories:
  - app/Helpers/
  - app/Libs/
    - Messages (exceptions, etc.)
    - Utils
- include ```sass``` boilerplate (explained at the end of this README)

### Additional Packages

- "filp/whoops" - better than CodeIgniter error reporting...
- "graham-campbell/markdown" - not really needed, but I use Markdown frequently, so I included this one
- "spatie/laravel-sluggable"
- (DEV) "laravel/dusk"
- (DEV) "phpunit/php-code-coverage"
- (DEV) "phpunit/phpunit"

### Route Separation

I don't like having all my routes defined under ```/routes/web.php```, so Route Separation usage should be encouraged. This only features Basic Split, though, but can be extended with custom rules. With this, rather than having everything registered in one file, all Routes can be split into separate files, grouped and prefixed separately.

// TODO

---

## Installation

Clone this repo somewhere:

```text
git clone https://github.com/DarkStoorM/laravel-boilerplate.git <your_project_name>

cd <your_project_name>

composer install
```

The composer installation will also run additional tasks:

- run ```npm i```
- run ```npm audit```
- compile assets
- create a new .env and Dusk .env (APP_URL in these files is set to ```http://127.0.0.1:8000``` by default)
- generate new app keys

**From Laravel/Dusk**: set the `APP_URL` environment variable in your application's .env file. **This value should match the URL you use to access your application in a browser** - ```http://127.0.0.1:8000``` in most cases. Change this address if you serve your files from dirrefent address.

If you wish to re-generate .env files at some point, you can run one of the following commands:

- ```composer copy-env```
- ```composer copy-env-dusk```

These commands will replace the existing .env files or create new ones. Use this only if you need a fresh copy!

### Database

Check the created ```.env``` for more information. SQLite is set by default, but you may also change to a different driver.

---

## Testing

### Browser Tests

Refer to [Laravel/Dusk](https://laravel.com/docs/8.x/dusk) when creating new test.

Also refer to [Chrome-Driver version](https://laravel.com/docs/8.x/dusk#managing-chromedriver-installations) - in case your environment depends on a different driver version.

Before running the tests:

- **make sure the server is running** either via built-in PHP development server or ```php artisan serve```
- make sure you have **Xdebug** installed

### Xdebug

To "install" Xdebug, follow these steps:

- Run ```php -i```
- Visit [Xdebug Installation Wizard](https://xdebug.org/wizard) and paste the contents of the ```php -i``` output
- The Wizard will suggest the proper binaries, and the required modifications to ```php.ini```

The paths may vary, make sure to specify the proper path to Xdebug extension in your ```php.ini```

```zend_extension = FULL_PATH_TO_EXTENSION_FROM_WIZARD```

You also have to add the following line to enable the Code Coverage

```xdebug.mode=coverage```

### Code Coverage

After installing Xdebug, the ```Code Coverage``` can viewed through Composer (**on Windows!**) with the following commands:

- Firefox: ```composer coverage-ff```
- Chrome: ```composer coverage-chrome```

This is not required, **use this command if you are viewing the reports for the first time**.

By default, Code Coverage will generate reports for the following paths:

- ```./app/Http/Controllers```
- ```./app/Models```
- ```./app/Helpers```
- ```./app/Libs```
- ```./routes/web```

Add custom paths while developing.

### Test paths

Change paths to your likings in the root ```phpunit.xml```. By default it's:

- ```[./tests/Unit]``` Testsuite: Unit
- ```[./tests/Feature]``` Testsuite: Feature
- ```[./tests/Browser]``` Testsuite: Implementation (Dusk)

---

- Run the **Browser tests** with ```php artisan dusk```
- Run all tests regularly with ```php artisan test```
- Run **all tests** with ```composer test``` - this will generate Code Coverage

---

## Development

Usually, when dealing with SASS, I have my ```watcher``` running in the background:

```text
npm run watch
```

This project was created with SCSS-BEM in mind.

The css should be kept separated in the following manner:

```text
sass
|- bem
|   |- layout
|   |   |- <BEM_layout_files>
|   |- other
|   |   |- <BEM_other> (e.g. forms)
|- utils
|   |- functions.scss
|   |- mixins.scss
|- variables
|   |- colors.scss
|   |- globals.scss
|   |- <other>.scss
|- app.scss
```

### Routes

The routes were using new syntax (Laravel 8):

```php
Route::get('/', [IndexController::class, 'index'])->name('index');
```

but I don't like it, so old syntax **will also work**, the ```namespace``` is uncommented under ```RouteServiceProvider```.

```php
Route::get('/', "IndexController@index")->name('index');
```

---

## Suggestions

### VSCode

**Not really needed**, but for VSCode you can install the following extensions:

- Laravel Blade Formatter
- Laravel Blade Snippets
- Laravel Blade Spacer
- Laravel Extra Intellisense
- Laravel goto view
- Laravel Snippets
- PHP Parameter Hint
- VS DocBlockr

---

No Vue/etc at all, feel free to do whatever you want. Don't actually rely on this ¯\\_(ツ)_/¯
