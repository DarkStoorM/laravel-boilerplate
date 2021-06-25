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
 * TODO!!!
 */

// Index Routes
Route::prefix('/')->group(__DIR__ . '/index.php');

// Admin Routes Group
$adminPathPrefix = __DIR__ . $adminPrefix = "/admin";
Route::prefix($adminPrefix)
    ->group(function () use ($adminPathPrefix, $adminPrefix) {
        Route::prefix('/')->group($adminPathPrefix . '/admin.php');
        Route::prefix('/statistics')->group($adminPathPrefix . '/statistics.php');

        // Forum Settings Routes Group
        $adminForumPathPrefix = __DIR__ . $adminForumPrefix = $adminPrefix . "/forum-settings";
        Route::prefix($adminForumPrefix)->group(function () use ($adminForumPathPrefix, $adminForumPrefix) {
            Route::prefix('/')->group($adminForumPathPrefix . '/forum-settings.php');
            Route::prefix('/threads')->group($adminForumPathPrefix . '/threads.php');
            Route::prefix('/posts')->group($adminForumPathPrefix . '/posts.php');
            Route::prefix('/users')->group($adminForumPathPrefix . '/users.php');
        });
    });
