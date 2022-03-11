<?php

use App\Libs\Utils\NamedRoute;

return [
    /** Password Change Form */
    'password-reset' => [
        'form-header'      => 'Reset your password',
        'form-description' =>
        'Forgot your password? Request a password reset link by providing your email address below.',
        'request-email'    => 'Account email',
        'request-button'   => 'Request password reset',

        'change-password-header'    => 'Set a new password',
        'new-password'              => 'New Password',
        'new-password-confirmation' => 'Confirm your new password',
        'new-password-button'       => 'Set new password',
    ],

    'login' => [
        'email'       => 'Email',
        'form-header' => 'Sign In',
        'password'    => 'Password',
        'submit'      => 'Login',
    ],

    'account-creation' => [
        'email'                 => 'Email address',
        'email-confirmation'    => 'Confirm your email address',
        'form-header'           => 'Sign Up',
        'form-footer'           => "Got an account? <a href='" . route(NamedRoute::GET_SESSION_INDEX) . "'>Sign In</a>.", /* phpcs:ignore */
        'password'              => 'Password',
        'password-confirmation' => 'Confirm your password',
        'submit-button'         => 'Submit',
    ],
];
