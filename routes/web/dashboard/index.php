<?php

use Illuminate\Support\Facades\Route;

use App\Libs\Utils\NamedRoute;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name(NamedRoute::GET_DASHBOARD_INDEX);
