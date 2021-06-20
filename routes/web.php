<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;

Route::get('/', "IndexController@index")->name('index');
