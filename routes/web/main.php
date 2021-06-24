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
 * To make it a bit more clear, check the Structure below how to keep the Routes separated.
 * 
 * Let's assume we have a MyProfile page, which has different Controllers
 * - Dashboard
 * - Settings
 * - Stats
 * 
 * The structure of these Routes should be as following:
 * 
 * |-  routes
 * |   |-  web/
 * |   |   |- profile
 * |   |   |   |- settings.php
 * |   |   |   |- stats.php
 * |   |   |   |- dashboard.php
 * |   |   |- index.php (basic Index)
 * |   |   |- main.php (main file - route loader)
 * 
 * and below are the definitions of this structure:
 * 
 * $profileRoutePrefix = __DIR__ . '/profile';
 * Route::prefix('profile')->group($profileRoutePrefix . 'profile/dashboard.php');
 * Route::prefix('profile/stats')->group($profileRoutePrefix . 'profile/stats.php');
 * Route::prefix('profile/settings')->group($profileRoutePrefix . 'profile/settings.php');
 * 
 * also the following is valid for more readable group:
 * 
 * Route::prefix("profile")->group(function () {
 *     $profileRoutePrefix = __DIR__ . '/profile';
 * 
 *     Route::prefix('/')->group($profileRoutePrefix . '/dashboard.php');
 *     Route::prefix('/stats')->group($profileRoutePrefix . '/stats.php');
 *     Route::prefix('/settings')->group($profileRoutePrefix . '/settings.php');
 * });
 * 
 * NOTE: There is no need to prefix the routes inside the target files, this
 * is the only place the prefix is required
 * 
 * and URLs for those routes:
 * 
 * /profile
 * /profile/stats
 * /profile/settings
 * 
 * Middleware can be assigned either in the file itself - if not all routes need them -
 * or in this file, but it would be a good practice to keep middlewares in one file.
 * Route::prefix()->middleware()->group()
 * 
 * Moving this to RouteServiceProvider would be nice, but I haven't found a way ^^.
 */

/**
 * Index Routes
 */
Route::prefix('/')->group(__DIR__ . '/index.php');
