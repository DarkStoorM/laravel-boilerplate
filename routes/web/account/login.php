<?php

use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', 'SessionsController@sessionIndex')->name(NamedRoute::GET_SESSION_INDEX);
Route::post('/', 'SessionsController@sessionStore')
    ->name(NamedRoute::POST_SESSION_STORE);