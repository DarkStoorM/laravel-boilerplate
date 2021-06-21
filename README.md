# Laravel - Boilerplate

Simple, a bit preconfigured boilerplate with a purpose of Learning - do **not** rely on this code for your own purposes. This was supposed to be a blog, but there was too much setup and I'm too lazy to prepare each new Laravel project.

---

- [Laravel - Boilerplate](#laravel---boilerplate)
  - [Requirements](#requirements)
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

## Installation

```text
composer install
```

The composer installation will also run additional tasks:

- run npm i
- run npm audit
- compile assets
- create a new .env and Dusk .env
- generate new app keys

After installing npm dependencies, run `npm run development` to initially compile SASS, or just go straight for `npm run watch`.

**From Laravel/Dusk**: set the `APP_URL` environment variable in your application's .env file. **This value should match the URL you use to access your application in a browser** - ```http://127.0.0.1:8000``` in most cases.

If you wish to re-generate .env files, you can run one of the following commands:

```text
composer copy-env
```

```text
composer copy-env-dusk
```

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
- Visit [Xdebug Installation Wizard](https://xdebug.org/wizard) and paste the contents of the below command
- The Wizard will suggest the proper binaries, and the required modifications to ```php.ini```

The paths may vary, make sure to specify the proper path to Xdebug extension in your ```php.ini```

```zend_extension = FULL_PATH_TO_EXTENSION_FROM_WIZARD```

You also have to add the following line to enable the Code Coverage

```xdebug.mode=coverage```

### Code Coverage

After installing Xdebug, the Code Coverage can viewed through Composer (on Windows!) with the following commands:

- Firefox: ```composer coverage-ff```
- Chrome: ```composer coverage-chrome```

This is not required, **use this command if you are viewing the reports for the first time**.

By default, Code Coverage will generate reports for the following paths:

- ./app/Http/Controllers
- ./app/Models
- ./app/Helpers
- ./app/Libs
- ./routes/web

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

Usually, when dealing with SASS, I have my watcher running in the background:

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

but I don't like it, so old syntax **will also work**

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
