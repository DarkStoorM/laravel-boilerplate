<?php

use Illuminate\Support\Facades\Route;

use App\Libs\Utils\NamedRoute;
use App\Http\Controllers\AccountCreationsController;

Route::get('/', [AccountCreationsController::class, 'index'])->name(NamedRoute::GET_ACCOUNT_CREATION_INDEX);
Route::post('/', [AccountCreationsController::class, 'store'])->name(NamedRoute::POST_ACCOUNT_CREATION_STORE);

Route::get('verify/{token}/{email}', [AccountCreationsController::class, 'verify'])->name(NamedRoute::GET_ACCOUNT_CREATION_VERIFY);
Route::get('verify/', [AccountCreationsController::class, 'status'])->name(NamedRoute::GET_ACCOUNT_CREATION_STATUS);
