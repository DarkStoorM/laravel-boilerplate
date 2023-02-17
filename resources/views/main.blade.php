<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1"
          name="viewport">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.ts'])
</head>

<body>
    <a href="{{ route(NamedRoute::GET_INDEX) }}">Home</a>

    <h1>Welcome</h1>
</body>

</html>
