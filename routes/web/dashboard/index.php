<?php

use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', 'DashboardController@index')->name(NamedRoute::GET_DASHBOARD_INDEX);
