# Laravel - Simple Blog

Simple blog with a purpose of Learning.
This blog is PHP-only - do not rely on this code for your own purposes.

---

- [Laravel - Simple Blog](#laravel---simple-blog)
  - [Installation](#installation)
    - [Database](#database)
    - [ChromeDriver for Dusk](#chromedriver-for-dusk)
  - [Testing](#testing)
    - [Browser Tests](#browser-tests)
    - [Laravel Tests](#laravel-tests)
  - [Development](#development)

---

## Installation

```text
composer install
npm i
```

The composer installation will also run additional tasks:

- create a new .env and Dusk .env
- generate new app keys

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

### ChromeDriver for Dusk

Make sure your ChromeDriver is up-to-date with:

```text
php artisan dusk:chrome-driver
```

---

## Testing

### Browser Tests

Refer to [Laravel/Dusk](https://laravel.com/docs/8.x/dusk)

Also refer to [Chrome-Driver version](https://laravel.com/docs/8.x/dusk#managing-chromedriver-installations) in the future, but `composer install` 

Before running the tests, make sure the server is running either via built-in PHP development server or ```php artisan serve```.

Run the Browser tests with the following command.

```text
php artisan dusk
```

### Laravel Tests

---

## Development

Usually, when dealing with SCSS, I have my watcher running in the background:

```text
npm run watch
```

No Vue/etc at all, feel free to do whatever you want ¯\\_(ツ)_/¯