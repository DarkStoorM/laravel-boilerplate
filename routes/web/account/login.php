<?php

use Illuminate\Support\Facades\Route;

use App\Libs\Utils\NamedRoute;
use App\Http\Controllers\SessionsController;

Route::get('/', [SessionsController::class, 'index'])->name(NamedRoute::GET_SESSION_INDEX);
Route::post('/', [SessionsController::class, 'store'])->name(NamedRoute::POST_SESSION_STORE);
