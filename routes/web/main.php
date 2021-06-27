<?php

use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------------
 * 
 * This file contains all Route Definitions. New routes should __not__ be defined
 * in this file, as this file serves as a Route File Loader.
 * 
 * The main purpose is to keep the Routes separated.
 * 
 * Everyone has his own way of organizing Routes, so this file will be left undocumented
 * 
 */

// Index Routes
Route::prefix('/')->group(__DIR__ . '/index.php');
