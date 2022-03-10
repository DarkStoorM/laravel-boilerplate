<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Account Creation Translation File
    |--------------------------------------------------------------------------
    |
    | This file contains the translation definitions for the Account Creation page
    |
    */
    'created' => 'Your account has been created. Please check your email and verify your account.',
    'invalid-token' => 'Invalid verification code.',
    'expired-token' => 'Your verification token has expired, please re-create your account.',
    'verified' => 'Your account has been verified. You can now log into your account.',
    'verified-reminder' => 'If you have already verified your account, please login.',

    'verification-page-header' => 'Account verification',

    /**
     * VALIDATION
     */

    'validation' => [
        'email.required'  => 'Please provide an email address.',
        'email.email'     => 'Please provide a valid email address.',
        'email.confirmed' => 'Please confirm your email address.',
        'email.unique'    => 'An account with provided email address already exists.',

        'password.required' => 'Please provide your new password',
        'password.confirmed' => 'You must confirm your password',
    ],
];
