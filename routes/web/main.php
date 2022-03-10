<?php

use Illuminate\Support\Facades\Route;

// Index Routes
Route::prefix('/')->group(__DIR__ . '/index.php');

/*
 * All "account" Routes should require the user to __not__ be authenticated before accessing
 * These routes are used for Login/Creation/Reset purposes, which only require Guest middleware
 * 
 * The "account"/"dashboard" prefix can be changed if the login/dashboard routes have to be hidden,
 * this behavior can be seen in e-commerce (admin login) or blogs where login routes
 * are only used by the Admin
 */

// Uses "account" prefix by default if there was no custom auth route prefix
$authPrefix = env("AUTH_ROUTES_PREFIX", "account");

// Assertion in case the value was not set
assert(empty($authPrefix) === false, "AUTH_ROUTES_PREFIX was left uncommented with no value");

Route::prefix($authPrefix . "-login")->middleware("guest")->group(function () {
    // Directory name does not need to be updated with login route prefix
    $path = __DIR__ . "/account/";

    // Login Routes
    Route::prefix("/")->group($path . "login.php");

    // Account Creation routes
    Route::prefix("create")->group($path . "create.php");

    // Password Reset routes
    Route::prefix("reset")->group($path . "reset.php");
});

// Dashboard Routes are only accessible by authenticated users.
// This is not the "main" index, this is the front page of the Management System
Route::prefix($authPrefix . "-dashboard")->middleware("auth")->group(function () {
    $path = __DIR__ . "/dashboard/";

    // Main Dashboard Route definitions
    Route::prefix('/')->group($path . 'index.php');
});
