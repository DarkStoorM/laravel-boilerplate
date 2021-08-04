<?php

use App\Libs\Utils\RouteNames;
use Illuminate\Support\Facades\Route;

Route::get('/', "IndexController@index")->name(RouteNames::GET_INDEX);

// We don't care if the user was Authenticated or not, we should not require 
// any Middleware on logout route
Route::get('logout', 'SessionsController@sessionDestroy')->name(RouteNames::GET_SESSION_DESTROY);
