<?php

return [
    'account-verification' => [
        'body' => 'In order to log into your account, you need to verify it first. Please click the link below before it expires. Your activation link will be active for :timeToExpire minutes.', /* phpcs:ignore */
        'button'  => 'Verify Your Account',
        'header'  => 'Thank you for registering a new account!',
        'report'  => 'If you did not request this, please reply to this email and report it!',
        'subject' => 'Welcome to :app! Verify your account',
    ],

    'common' => [
        'hello'   => 'Hello, :user',
        'report'  => 'If you did not request this change, please reply to this email and report it.',
        /* this is a common greetings where we don't know the user's name yet */
        'welcome' => 'Welcome,',
    ],

    'password-reset' => [
        'body'    => 'If you have requested to reset your password, please click the button below:',
        'button'  => 'Change your password',
        'header'  => 'There was a new password reset request for the following email address - :email',
        'subject' => 'New Password Reset Request',
    ],
];
