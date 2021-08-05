<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Account Login Translation File
    |--------------------------------------------------------------------------
    |
    | This file contains the translation definitions for the Account Login page
    |
    */

    "failed"     => "Incorrect email address or password.",
    "unverified" => "You need to verify your email address first.",

    /**
     * VALIDATION
     */

    "validation" => [
        'email-invalid'     => 'Please provide a valid email address.',
        'email-required'    => 'Email address is required.',
        'password-required' => 'Password is required.',
        'throttled'         => 'Too many login attempts, please try again later.',
    ]
];
