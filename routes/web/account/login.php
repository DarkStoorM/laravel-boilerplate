<?php

use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', 'SessionsController@index')->name(NamedRoute::GET_SESSION_INDEX);
Route::post('/', 'SessionsController@store')
    ->name(NamedRoute::POST_SESSION_STORE);
