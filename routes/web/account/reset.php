<?php

use App\Http\Controllers\PasswordResetsController;
use App\Libs\Utils\NamedRoute;
use Illuminate\Support\Facades\Route;

// Shows the Password Reset Form view
Route::get('/', [PasswordResetsController::class, 'index'])->name(NamedRoute::GET_PASSWORD_RESET_INDEX);

// Sends an email with a reset link after validation
Route::post('/', [PasswordResetsController::class, 'storeRequest'])->name(NamedRoute::POST_PASSWORD_RESET_STORE);

// Validates the token in the password reset link
Route::group(['prefix' => 'activate'], function () {
    // This route will only display the reset link status
    // If the reset token was valid, a form will be displayed under this route
    Route::get('/', [PasswordResetsController::class, 'activate'])->name(NamedRoute::GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT);

    // This route only validates the token, which then redirects to the index of this group
    Route::get('/{token}/{email}', [PasswordResetsController::class, 'validateToken'])->name(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN);

    Route::group(['prefix' => 'change-password'], function () {
        // This route will display the result of the password change
        Route::get('/', [PasswordResetsController::class, 'changeStatus'])->name(NamedRoute::GET_PASSWORD_RESET_CHANGE_RESULT);

        // This route will display the password change form
        Route::get('/{token}/{email}', [PasswordResetsController::class, 'reset'])->name(NamedRoute::GET_PASSWORD_RESET_CHANGE_CREATE);

        // This route will validate the password change request, then redirect to the results
        // TODO
        Route::post('/{token}/{email}', [PasswordResetsController::class, 'update'])->name(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE);
    });
});
