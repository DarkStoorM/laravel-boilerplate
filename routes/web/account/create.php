<?php

use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', 'AccountCreationsController@index')->name(NamedRoute::GET_ACCOUNT_CREATION_INDEX);
Route::post('/', 'AccountCreationsController@store')->name(NamedRoute::POST_ACCOUNT_CREATION_STORE);

Route::get('verify/{token}/{email}', 'AccountCreationsController@verify')->name(NamedRoute::GET_ACCOUNT_CREATION_VERIFY);
Route::get('verify/', 'AccountCreationsController@status')->name(NamedRoute::GET_ACCOUNT_CREATION_STATUS);
