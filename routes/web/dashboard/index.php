<?php

use App\Http\Controllers\DashboardController;
use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name(NamedRoute::GET_DASHBOARD_INDEX);
