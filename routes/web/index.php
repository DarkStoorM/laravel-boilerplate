<?php

use App\Http\Controllers\IndexController;
use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get(
    '/',
    [IndexController::class, 'index']
)->name(NamedRoute::GET_INDEX);
