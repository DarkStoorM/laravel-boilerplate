<?php

use App\Http\Controllers\SessionsController;
use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionsController::class, 'index'])->name(NamedRoute::GET_SESSION_INDEX);
Route::post('/', [SessionsController::class, 'store'])->name(NamedRoute::POST_SESSION_STORE);
