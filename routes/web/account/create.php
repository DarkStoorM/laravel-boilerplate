<?php

use App\Libs\Utils\RouteNames;
use Illuminate\Support\Facades\Route;

Route::get('/', 'AccountCreationsController@index')->name(RouteNames::GET_ACCOUNT_CREATION_INDEX);
Route::post('/', 'AccountCreationsController@store')->name(RouteNames::POST_ACCOUNT_CREATION_STORE);

Route::get('verify/{token}/{email}', 'AccountCreationsController@verify')->name(RouteNames::GET_ACCOUNT_CREATION_VERIFY);
Route::get('verify/', 'AccountCreationsController@status')->name(RouteNames::GET_ACCOUNT_CREATION_STATUS);
