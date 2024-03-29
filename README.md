# Laravel - Boilerplate

![img](https://img.shields.io/badge/-DISCONTINUED-red)

Simple, a bit pre-configured boilerplate with a purpose of Learning - do **not** rely on this code for your own purposes, as this README is a bit messy and the explanation probably won't cover everything. Feel free to review and fix this repo :)

This was supposed to be a blog, but there was too much setup and I'm too lazy to prepare each new Laravel project. Some steps require manual work, since they depend on the environment configuration.

> Update on 02.02.2023 - I made a custom authentication just for practice purposes, but I decided to remove it to not have to clean the project up every time ¯\\\_(ツ)\_/¯ I moved it into a separate branch: [custom-authentication](https://github.com/DarkStoorM/laravel-boilerplate/tree/custom-authentication)
>
> Update on 10.20.2023 - project discontinued, too much stuff has changed, rendering this boilerplate useless with new packages

---

- [Laravel - Boilerplate](#laravel---boilerplate)
  - [Requirements](#requirements)
    - [Manual configuration](#manual-configuration)
  - [Differences between a fresh Laravel install and this repo](#differences-between-a-fresh-laravel-install-and-this-repo)
    - [Pre-configuration](#pre-configuration)
    - [Additional Packages](#additional-packages)
      - [Composer](#composer)
      - [Node](#node)
    - [Additional Libraries](#additional-libraries)
      - [Code Execution Time Measurement](#code-execution-time-measurement)
      - [Constants](#constants)
      - [Route Naming](#route-naming)
    - [Route Separation](#route-separation)
    - [Hidden Login/Dashboard Route](#hidden-logindashboard-route)
    - [Mailing](#mailing)
  - [Installation](#installation)
    - [Database](#database)
  - [Running](#running)
  - [PHP Code Sniffer](#php-code-sniffer)
  - [Testing](#testing)
    - [Browser Tests](#browser-tests)
    - [Xdebug](#xdebug)
    - [Code Coverage](#code-coverage)
    - [Test paths](#test-paths)
    - [Testing Mailables](#testing-mailables)
      - [Sending Emails with Gmail SMTP](#sending-emails-with-gmail-smtp)
    - [Vitest](#vitest)
  - [Development](#development)
    - [Localization](#localization)
      - [Validation messages](#validation-messages)
      - [External translation file](#external-translation-file)
    - [Routes](#routes)
  - [Suggestions](#suggestions)
    - [VSCode](#vscode)

---

## Requirements

- PHP ^8.1
- Composer 2.0
- NodeJS
- SQLite
- PHP Code Sniffer (Squizlabs) - `composer global require "squizlabs/php_codesniffer"`
- Xdebug (for Code Coverage - not really a requirement, but it's good to have for testing)

For VSCode, install **Ioannis-kappas/phpcs**.

### Manual configuration

Some files require **manual** configuration depending on your environment. Although, the default config in this project should be enough, some projects might have different preferences. Files to review (if you need to):

- .env
- .env.testing
- .env.dusk.local
- phpunit.xml
- phpunit.dusk.xml
- tsconfig.json
  
There is no TypeScript rule configuration, because all projects are different.

---

## Differences between a fresh Laravel install and this repo

**This is (obviously) not a professionally prepared repository** for all Laravel applications, but rather a quick pseudo-pre-configuration. Most of the *manual* tasks are not even needed, like installing Xdebug for Code Coverage, but since Laravel is perfect for TDD, having Code Coverage available is always nice.

**Supports TypeScript and Vite/Vitest**.

> The existing template is fully localized with `@lang`. Since there is no user input, there is nothing that needs to be escaped.

### Pre-configuration

What actually is this "pre-configuration"? They are some simple steps **for basic applications**:

- npm dependency install
- npm audit fix
- create new `.env` files
- generate APP_KEY for regular and Dusk `.env` files
- require Dusk for --dev
- use SQLite by default (set by ```.env```)
- use ```log``` Mail Driver - note, that `MAIL_DRIVER` **has been changed to `MAIL_MAILER` since Laravel 7**
- prepare the following directories (1):
  - app/Helpers/
  - app/Libs/
    - Messages (exceptions, etc.) (2)
    - Utils (I use these in my projects for various "smaller" classes)
- include ```sass``` boilerplate (explained at the end of this README)

> (1) as custom authentication has been removed, old Helpers have also been removed (token generation/date helpers)
>
> (2) I always extract system messages into separate class static strings, except where the messages have to be shown to the user - those go into the localization

`View Composers` can not be pre-configured, but refer to [the docs here](https://laravel.com/docs/9.x/views#view-composers) if you need them.

### Additional Packages

#### Composer

- "graham-campbell/markdown" - not really needed, but I use Markdown frequently, so I included this one (blogs / mails / etc.)
- "spatie/laravel-sluggable" - useful for blogs, etc
- (DEV) "spatie/laravel-ignition" - reverted from "filp/whoops"
- (DEV) "laravel/dusk"
- (DEV) "phpunit/php-code-coverage"
- (DEV) "phpunit/phpunit"
- (DEV) "slevomat/coding-standard"
- (DEV) "squizlabs/php_codesniffer"

#### Node

- (DEV) `ts-node` - TypeScript environment. Run `npx ts-node` to create a TypeScript *playground*
- (DEV) `vitest` - switched from TS Jest
- (DEV) `vite` - for SCSS/TS code builds

### Additional Libraries

#### Code Execution Time Measurement

As a small experiment, I've written and included a small script to measure the ```code execution time``` for blocks of code and functions, which is also "fully" tested.

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

#### Constants

I like having a global Constants file. I know it's not an ideal solution and there should be some kind of provider for that - I like simple solutions.

The file resides under `App\Libs\Constants`

#### Route Naming

There is a thing I don't like: having hard-coded Route names. While it's easy to grab them with VSCode extensions (route name resolving), I like having the route names ready-to-use from a set of constants, so the route names can be referenced simply by a class constant everywhere. This is a nice feature, especially for testing, where same route names are used multiple times.

The file resides under `App\Libs\Utils\NamedRoute`. As explained in the file, this can be completely skipped. The library is registered, so it's available in the Views without cluttering the Blade Templates with namespaces.

### Route Separation

I don't like having all my routes defined under ```/routes/web.php```, so Route Separation usage should be encouraged. This only features Basic Split, though, but can be extended with custom rules or some advanced magic. With this, rather than having everything registered in one file, all Routes can be split into separate files, grouped and prefixed separately. This obviously can be done better, but for my tiny needs it works as intended.

> The template contains Account/Dashboard routes, which don't have the corresponding Controllers, but these routes have been left inside to serve as a template.

I wanted to kind of enforce Route Separation, but since it's a matter of personal preference, read this instead: [Laravel Route Separation experiment](https://gist.github.com/DarkStoorM/fadf4297d4871e3df0d580e0e96cf8bf).

### Hidden Login/Dashboard Route

Sometimes you would want to hide the `login` or `dashboard` routes, keeping them completely separate from other routes. This is useful for applications, which do not want to expose some of the endpoints to regular users.

This is mostly applied in e-commerce, where `Admin Dashboards` are only meant to be accessible by the *authorized* users (given the dashboard link, not to confuse with logged in).

This template allows changing the route prefix of all Login and Dashboard routes in the `.env` file:

```text
# -- Custom Auth Route Prefix --
# Change this if your login/Dashboard routes have to be hidden from the outside world
# example: AUTH_ROUTES_PREFIX=622a1ddfb575d622a1ddfb5760
# route output: localhost:8000/622a1ddfb575d622a1ddfb5760-login/
# route output: localhost:8000/622a1ddfb575d622a1ddfb5760-dashboard/
```

By default, the `Auth Prefix` will use `account` string, which leaves the auth routes as following:

```text
http://localhost:8000/account-login/
http://localhost:8000/account-dashboard/
```

Changing this prefix gives the following output:

```text
http://localhost:8000/622a1ddfb575d622a1ddfb5760-login/
http://localhost:8000/622a1ddfb575d622a1ddfb5760-dashboard/
```

Both Login and Dashboard uses a prefix on purpose - in case someone hits a `/dashboard` route unauthorized, getting redirected to a *hidden* login route.

### Mailing

Laravel makes it really easy to send emails, even Markdown emails are supported. For a new `Mailable` use the following command:

```plaintext
php artisan make:mail Mailable<Something>

// Where <Something> is your Mailable name, like MailableNotification.
```

To use Markdown emails - appending a `--markdown=path.to.view` argument creates a template for Markdown Email under `/resources/views`. The `path.to.view` can evaluate to `/emails/section/final-view`, for example:

```plaintext
php artisan make:mail MailableVerification --markdown=emails.account.verification
```

The `php artisan make:mail` command will create a new Mailable class under `/app/Mail`.

For more information about Mailables, please refer to the [Laravel 9.x/mail docs](https://laravel.com/docs/9.x/mail).

By default, mails will be Logged to file. Configure your `/app/logging.php` to change where your logged emails go. They will appear under `/storage/logs/laravel.log` - the emails will be rendered as they would be sent to the user.

For testing please refer to [Testing Mailables](#testing-mailables)

---

## Installation

Clone this repo somewhere:

```text
git clone https://github.com/DarkStoorM/laravel-boilerplate.git <your_project_name>

cd <your_project_name>

composer install
```

**From Laravel/Dusk**: set the `APP_URL` environment variable in your application's .env file. **This value should match the URL you use to access your application in a browser** - ```http://127.0.0.1:8000``` in most cases. Change this address if you serve your files from different address.

If you wish to re-generate .env files at some point, you can run one of the following commands:

- ```composer copy-env```
- ```composer copy-env-testing```
- ```composer copy-env-dusk```

**These commands will replace the existing .env files** or create new ones. Use this only if you need a fresh copy! Remember to regenerate the application key with `php artisan key:generate`

### Database

Check the created ```.env``` for more information. SQLite is set by default, but you may also change to a different driver.

For a quick database, create a new file under ```/database/db.sqlite``` directory or type ```type NUL > database/db.sqlite``` (windows).

---

## Running

Start the server with the following command: - ```composer start```

**It is important to configure the environment files as well as ```phpunit.xml|phpunit.dusk.xml``` files, since different machines require different configuration!** - the default configuration should be enough to allow a quick start.

---

## PHP Code Sniffer

This template uses `squizlabs/php_codesniffer` along with `slevomat/coding-standard`. Head over to `/phpcs.xml` to adjust the Code Sniffer rules.

- execute `composer format` to run the code sniffer analysis
- execute `composer format-fix` to attempt to automatically fix some of the found issues

Just in case, check your VSCode's `settings.json` if your  code sniffer is enabled:

```json
"phpcs.enable": true,
```

---

## Testing

### Browser Tests

Refer to [Laravel/Dusk](https://laravel.com/docs/9.x/dusk) when creating new test.

Also refer to [Chrome-Driver version](https://laravel.com/docs/9.x/dusk#managing-chromedriver-installations) - in case your environment depends on a different driver version. The `composer install` should automatically resolve this.

**Before running the tests**:

- **make sure the server is running** either via built-in PHP development server or ```composer start``` (Dusk only)
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

> Both commands are also available for NPM to open the Vitest code coverage reports - use ```npm run coverage-ff``` or ```npm run coverage-chrome```

This is not required, **use this command if you are viewing the reports for the first time**.

By default, Code Coverage will generate reports for the entire ```./app```. Customize the paths under ```phpunit.xml```, since most of the source is automatically covered under the hood (Middleware/Services/etc.)

### Test paths

Change paths to your likings in the root ```phpunit.xml```. By default it's:

- ```[./tests/Unit]``` Testsuite: Unit
- ```[./tests/Feature]``` Testsuite: Feature
- ```[./tests/Routes]``` Testsuite: Routes

Test names are now camelCase, in the previous release snake_case was used.

### Testing Mailables

Laravel provides a bunch of methods for testing Mailables, which does not require you to actually send an email to the user in order to check the rendered content. You should test the mailable content separately if you also need to test if emails can be sent.

Refer to the Docs for [testing mailable content](https://laravel.com/docs/9.x/mail#testing-mailables) or [Faking the Mail Send](https://laravel.com/docs/9.x/mocking#mail-fake).

#### Sending Emails with Gmail SMTP

If you would want to send emails through Gmail SMTP, the configuration is really simple:

- Create a Gmail account
- Configure your `.env<env>` as following

   ```text
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.googlemail.com
   MAIL_PORT=465
   MAIL_USERNAME=ENTER_YOUR_EMAIL_ADDRESS(GMAIL)
   MAIL_PASSWORD=ENTER_YOUR_GMAIL_PASSWORD
   MAIL_ENCRYPTION=ssl
   ```

- Navigate to `Manage Google Account` -> `Security` tab -> Scroll down to `Less secure app access` settings and turn it `on`.

Gmail Mailer should be ready to go. **Don't use this in production, though.**

---

I usually run all test at once, for that I added ```composer test``` command. Below is the list of available commands for running tests:

- Run the **Browser tests** with ```php artisan dusk```
- Run **all tests** with ```php artisan test``` - no Code Coverage, different output
- Run **all tests** with ```composer test``` - **this will generate Code Coverage**

If there is an argument to include to `php artisan test`, which also generates CodeCoverage, please let me know.

---

### Vitest

> Previously, this project was depending on `Jest` for testing, but since Laravel 9.5~, Vite is the default instead of Laravel Mix, so switching to Vitest was a good idea. It's faster both for code builds and tests anyway.

Vitest is included with some basic configuration and code coverage.

Run tests with Code Coverage with the following command

```plaintext
npm run test-dev
```

Omitting `-dev` prefix skips generating the code coverage report.

Code coverage will reside under `tests/reports`.

TypeScript tests reside under `tests/Node`.

---

## Development

Usually, when dealing with SASS, I have my ```watcher``` running in the background:

```text
npm run watch
```

This project was created with SCSS-BEM in mind. Although, the current structure seems to be a bit messy.

```text
sass
├─ bem
│   ├─ layout
│   │   └─ <BEM_layout_files>
│   ├─ links.scss
│   └─ properties.scss
├─ utils
│   ├─ functions.scss
│   └─ mixins.scss
├─ variables
│   ├─ colors.scss
│   ├─ fonts.scss
│   ├─ globals.scss
│   └─ <other>.scss
├─ app.scss
```

`sass/bem/layout/` - This directory contains some definitions, like a basic container, form elements (just a little, not fully stylized!).

`sass/bem/links.scss` - I put the Links styles separately, I tend to have many different link styles, so I just keep them in one file.

### Localization

#### Validation messages

I like having custom validation messages and I always group all the translation keys with the page localization for example: `login.php` translation file contains all the text that should be visible on the `Login Page`, including `validation error messages`.

Validation messages are always declared under `/app/Http/Requests` in FormRequests. I sometimes swap attributes in `FormRequest` too, but there was no need for this in this project.

#### External translation file

There is a small problem with one of the Validators. The new [Password](https://github.com/laravel/framework/pull/36960) object can not be translated through the Translator, it has to be string-based. For that, check `/resources/lang/en.json` - the Password object translation is stored in this file until Laravel resolves this issue.

### Routes

The routes are now using the new syntax (Laravel 8) - I guess for intellisense (?):

```php
Route::get('/', [IndexController::class, 'index'])->name('index');
```

The old syntax **will also work**, the ```namespace``` is uncommented under ```RouteServiceProvider```.

```php
Route::get('/', 'IndexController@index')->name('index');
```

I switched to the new syntax for the extra intellisense. The switch is pretty much straightforward (**the following does not apply to this project, leaving this only to show the migration steps**):

⚠️ warning: use this under the Source Control since there is no `Undo Replace All` in case something goes wrong ⚠️

**Only in VSCode, I guess:**

- `ctrl+shift+h`
- Search Phrase (regex): `['"](\w+Controller)@(\w+)['"]`
- Replace: `[$1::class, '$2']`
- ⚠️ **verify the replace** ⚠️
- `ctrl+alt+enter` in the Replace field to **replace all**

This will perform a replace in your routes files:

![replace](https://i.imgur.com/xcb9GsH.png)

![replace](https://i.imgur.com/SCFCpYq.png)

**This, sadly, requires you to use the fully qualified Controller class names.**

**PHP Namespace Resolver** (**mehedidracula.php-namespace-resolver**) can help with this in VSCode.

- Open your Routes file
- Open Command Palette: `import all classes`
- When duplicate classes are present, you can pick which class to import from the list

![import](https://i.imgur.com/wTz8X7k.png)

---

## Suggestions

### VSCode

**Not really needed**, but for VSCode you can install the following extensions:

- Laravel Blade Formatter `shufo.vscode-blade-formatter`
- Laravel Blade Snippets `onecentlin.laravel-blade`
- Laravel Blade Spacer `austenc.laravel-blade-spacer` Automatically inserts spaces between curly braces in Blade
- Laravel Extra Intellisense `amiralizadeh9480.laravel-extra-intellisense`
- Laravel goto view - `codingyu.laravel-goto-view` ctrl+click a **valid** view to navigate to the file. If the path is correct, the view name will be underlined
- Laravel Snippets `onecentlin.laravel5-snippets`
- PHP Parameter Hint `robertgr991.php-parameter-hint`
- VS DocBlockr `jeremyljackson.vs-docblock` (for most languages) or use `neilbrayfield.php-docblocker` for PHP - or both, but switch them in settings.json
- PHP Namespace Resolver `mehedidracula.php-namespace-resolver` - allows to auto-import missing classes

Note on PHP Parameter Hint: install this only if you like the parameter labels. It tends to be buggy (not removing the hints while deleting lines of code, requires switching tabs)

![img](https://i.imgur.com/ohpX6QP.png)

---

No Vue/etc at all, feel free to do whatever you want. Don't actually rely on this ¯\\*(ツ)*/¯
