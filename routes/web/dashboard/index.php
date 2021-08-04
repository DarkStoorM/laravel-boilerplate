<?php

use App\Libs\Utils\RouteNames;
use Illuminate\Support\Facades\Route;

Route::get('/', 'DashboardController@index')->name(RouteNames::GET_DASHBOARD_INDEX);
