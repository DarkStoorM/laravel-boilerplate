<?php

use App\Libs\Utils\RouteNames;
use Illuminate\Support\Facades\Route;

Route::get('/', 'SessionsController@sessionIndex')->name(RouteNames::GET_SESSION_INDEX);
Route::post('/', 'SessionsController@sessionStore')
    ->name(RouteNames::POST_SESSION_STORE);
