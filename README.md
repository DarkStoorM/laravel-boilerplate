# Laravel - Boilerplate

![img](https://img.shields.io/badge/-WIP-red)

Simple, a bit pre-configured boilerplate with a purpose of Learning - do **not** rely on this code for your own purposes. This was supposed to be a blog, but there was too much setup and I'm too lazy to prepare each new Laravel project. Some steps require manual work, since they depend on the environment configuration.

---

- [Laravel - Boilerplate](#laravel---boilerplate)
  - [Requirements](#requirements)
    - [Manual configuration](#manual-configuration)
  - [Differences between a fresh Laravel install and this repo](#differences-between-a-fresh-laravel-install-and-this-repo)
    - [Pre-configuration](#pre-configuration)
    - [Additional Packages](#additional-packages)
    - [Additional Libraries](#additional-libraries)
    - [Route Separation](#route-separation)
  - [Installation](#installation)
    - [Database](#database)
  - [Running](#running)
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
- NodeJS
- SQLite
- Xdebug (for Code Coverage - not really a requirement, but it's good to have)

### Manual configuration

Some files require **manual** configuration depending on your environment. Although, the default config in this project should be enough, but some projects might have different preferences. Files to configure (if you need to):

- ```.env``` (1)
- ```.env.dusk.local``` (2)
- ```phpunit.xml``` (3)
- ```phpunit.dusk.xml``` (4)
- ```.\config\database.php``` (5)

```text
You are required to verify the database configuration to make tests work
(1) / (2) - Both '.env' and '.env.dusk` use the same config, database path is the difference
(3) / (4) - every environment has different configuration requirements or just preferences
(5) - Database paths for SQLite have been changed. Verify this on your machine
```

---

## Differences between a fresh Laravel install and this repo

**This is not a professionally prepared repository** for all Laravel applications, but rather a quick pseudo-pre-configuration. Most of the *manual* tasks are not even needed, like installing Xdebug for Code Coverage, but since Laravel is perfect for TDD, having Code Coverage available is always nice.

### Pre-configuration

What actually is this "pre-configuration"? They are some simple steps **for basic applications**:

- npm dependency install
- npm audit fix
- create new ```.env``` files
- generate APP_KEY for regular and Dusk ```.env``` files
- require Dusk for --dev
- Example Feature Test is now IndexTest (I always prepare Index Tests in this place), but **this can be deleted**
- use SQLite by default (set by ```.env```)
- create ```.\database\db.sqlite``` and ```.\database\db_dusk.sqlite```
- prepare the following directories:
  - app/Helpers/ (a generic Helper file is created just for the autoload)
  - app/Libs/
    - Messages (exceptions, etc.)
    - Utils
- include ```sass``` boilerplate (explained at the end of this README)

`View Composers` can not be pre-configured, but refer to [the docs here](https://laravel.com/docs/8.x/views#view-composers) if you need them.

### Additional Packages

- "graham-campbell/markdown" - not really needed, but I use Markdown frequently, so I included this one
- "spatie/laravel-sluggable"
- ```DEV``` -  "filp/whoops" - better than CodeIgniter error reporting...
- ```DEV``` - "Barryvdh\Debugbar"
- ```DEV``` - "laravel/dusk"
- ```DEV``` - "phpunit/php-code-coverage"
- ```DEV``` - "phpunit/phpunit"

### Additional Libraries

As a small experiment, I've written and included a small script to measure the ```code execution time``` **for blocks of code and functions**.

The rest is handled by Debugbar.

Usage:

```php
// Testing a function
$test = function () {
  // Assuming there is a test() function declared somewhere
  test();
}

// -- Check the class for more information
$timer = new ExecutionTimeMeasurement(
  "Test Function" /* result message (optional) */,
  false           /* start immediately (optional) */,
  $test           /* closure (optional) - can also be replaced with anonymous function */
);

// Result: "Test Function - 13μs"
echo $timer->getResult();

// -------
// Testing a block of code

// Start the timer right after the initialization
$timer = new ExecutionTimeMeasurement("Some Code", true);
for ($i = 0; $i < 1000000; $i++);
$result = $timer->getResult();

// Result: "Some Code - 102.48ms"
echo $result;
```

### Route Separation

I don't like having all my routes defined under ```/routes/web.php```, so Route Separation usage should be encouraged. This only features Basic Split, though, but can be extended with custom rules. With this, rather than having everything registered in one file, all Routes can be split into separate files, grouped and prefixed separately.

I wanted to kind of enforce Route Separation, but since it's a matter of personal preference, read this instead: [Laravel Route Separation experiment](https://gist.github.com/DarkStoorM/fadf4297d4871e3df0d580e0e96cf8bf).

---

## Installation

Clone this repo somewhere:

```text
git clone https://github.com/DarkStoorM/laravel-boilerplate.git <your_project_name>

cd <your_project_name>

composer install
```

```composer install``` will also execute the following:

- run ```npm i```
- run ```npm audit```
- compile assets
- create a new .env and Dusk .env (APP_URL in these files is set to ```http://127.0.0.1:8000``` by default)
- generate new app keys
- install Chrome Drivers for Dusk
- create database files
- migrate default tables
- seed the default database

**From Laravel/Dusk**: set the `APP_URL` environment variable in your application's .env file. **This value should match the URL you use to access your application in a browser** - ```http://127.0.0.1:8000``` in most cases. Change this address if you serve your files from different address.

If you wish to re-generate .env files at some point, you can run one of the following commands:

- ```composer copy-env```
- ```composer copy-env-dusk```

These commands will replace the existing .env files or create new ones. Use this only if you need a fresh copy!

### Database

Check the created ```.env``` for more information. SQLite is set by default, but you may also change to a different driver. ```DB_Database``` will be ignored for SQLite - it tends to not work on different machines. Default ```'database' => database_path('db.sqlite')``` is used under ```config/database.php```.

For a quick database, create a new file under ```/database/db.sqlite``` directory or type ```type NUL > database/db.sqlite``` (windows).

**NOTICE**: *there are no additional migrations, only default one are left untouched.*

---

## Running

There are two scripts allowing to run the server

- ```composer start```
- ```composer start-dusk```

Those commands will run on different environments. ```composer start``` will use ```.env``` environment file, while ```composer start-dusk``` points at ```.env.dusk.local```.

**It is important to configure both environment files as well as ```phpunit.xml|phpunit.dusk.xml``` files, since different machines require different configuration!**

---

## Testing

### Browser Tests

Refer to [Laravel/Dusk](https://laravel.com/docs/8.x/dusk) when creating new test.

Also refer to [Chrome-Driver version](https://laravel.com/docs/8.x/dusk#managing-chromedriver-installations) - in case your environment depends on a different driver version.

Before running the tests:

- **make sure the server is running** either via built-in PHP development server or ```composer start-dusk``` - this will run a composer script executing ```php artisan dusk --env=.env.dusk.local```
- make sure you have **Xdebug** installed

Dusk Tests can be filtered with groups. The example has been left under ```./tests/Browser/IndexTest.php```.

Executing ```php artisan dusk --group=index``` will only execute tests with the specified **group** filter.

```php
/**
 * @group GROUP_NAME
 */
public function test_something(): void
{
  // to run this test (or a group of tests tagged with GROUP_NAME) execute
  // php artisan dusk --group=GROUP_NAME
}
```

```php
/**
 * Asserts that user visiting the index route will see a piece of text that
 * is hardcoded for now and serves only as an example to check if Browser Tests
 * are working correctly
 * 
 * @group index
 */
public function test_userCanSeeHelloOnMainPage(): void
{
    $this->browse(function (Browser $browser) {
        $browser->visit(route("index"))
            ->assertSee('hello');
    });
}
```

Few words on the test naming:

There is no forced convention, but snake case should be the best choice. As you can see in the example above, long names with ```camelCase``` are quite unreadable...

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

Add custom paths while developing.

### Test paths

Change paths to your likings in the root ```phpunit.xml```. By default it's:

- ```[./tests/Unit]``` Testsuite: Unit
- ```[./tests/Feature]``` Testsuite: Feature
- ```[./tests/Browser]``` Testsuite: Implementation (Dusk)

---

I usually run all test at once, for that I added ```composer test``` command. This will also run Browser tests (I modified phpunit.xml). Below is the list of available commands for running tests:

- Run the **Browser tests** with ```php artisan dusk```
- Run all tests with ```php artisan test``` - no Code Coverage, different output
- Run **all tests** with ```composer test``` - this will generate Code Coverage. After the tests are done, this will automatically **migrate** and **seed** the database with the default seeder

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
├─ bem
│   ├─ layout
│   │   └─ <BEM_layout_files>
│   ├─ other
│   │   └─ <BEM_other> (e.g. forms)
├─ utils
│   ├─ functions.scss
│   └─ mixins.scss
├─ variables
│   ├─ colors.scss
│   ├─ globals.scss
│   └─ <other>.scss
├─ app.scss
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

- Laravel Blade Formatter [**shufo.vscode-blade-formatter**]
- Laravel Blade Snippets [**onecentlin.laravel-blade**]
- Laravel Blade Spacer [**austenc.laravel-blade-spacer**] Automatically inserts spaces between curly braces in Blade
- Laravel Extra Intellisense [**amiralizadeh9480.laravel-extra-intellisense**]
- Laravel goto view - [**codingyu.laravel-goto-view**] ctrl+click in the ```Controller``` to navigate to the ```view```
- Laravel Snippets [**onecentlin.laravel5-snippets**]
- PHP Parameter Hint [**robertgr991.php-parameter-hint**]
- VS DocBlockr [**jeremyljackson.vs-docblock**] (for most languages) or use [**neilbrayfield.php-docblocker**] for PHP - or both, but switch them in settings.json

Note on PHP Parameter Hint: install this only if you like the parameter labels. It tends to be buggy (not removing the hints while deleting lines of code). If the parameter hints are still visible after deleting the code, depending on your settings, usually saving the file **or** switching Tabs will delete them (assuming there are no errors in the currently opened file).

![img](https://i.imgur.com/ohpX6QP.png)

---

No Vue/etc at all, feel free to do whatever you want. Don't actually rely on this ¯\\_(ツ)_/¯
