<?php

use App\Libs\Utils\NamedRoute;

return [

    /*
    |--------------------------------------------------------------------------
    | Links Translation File
    |--------------------------------------------------------------------------
    |
    | This file contains the structure of link translations, grouped by the page type
    |
    */

    'index' => [
        'about'     => 'About',
        'contact'   => 'Contact',
        'dashboard' => 'Dashboard',
        'login'     => 'Sign In',
        'logout'    => 'Logout',
        'signup'    => 'Sign Up',
    ],

    'login' => [
        'forgot-password' => 'Forgot your password?',
        'sign-up'         => 'Need an account? <a href=' . route(NamedRoute::GET_ACCOUNT_CREATION_INDEX) . '>Sign Up</a>.', /* phpcs:ignore */
    ],
];
