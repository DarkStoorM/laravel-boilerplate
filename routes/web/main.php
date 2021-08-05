<?php

use Illuminate\Support\Facades\Route;

// Index Routes
Route::prefix('/')->group(__DIR__ . '/index.php');

// All "account" Routes should require the user to __not__ be authenticated before accessing
// These routes are used for Login/Creation/Reset purposes, which only require Guest middleware
Route::prefix("account")->middleware("guest")->group(function () {
    $path = __DIR__ . "/account/";

    // Login Routes
    Route::prefix("login")->group($path . "login.php");

    // Account Creation routes
    Route::prefix("create")->group($path . "create.php");

    // Password Reset routes
    Route::prefix("reset")->group($path . "reset.php");
});

// Dashboard Routes are only accessible by authenticated users.
// This is not the "main" index, this is the front page of the Management System
Route::prefix("dashboard")->middleware("auth")->group(function () {
    $path = __DIR__ . "/dashboard/";

    // Main Dashboard Route definitions
    Route::prefix('/')->group($path . 'index.php');
});
