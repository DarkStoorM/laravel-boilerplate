<?php

use App\Libs\Constants;

return [
    'invalid-token'    => 'This password reset token is either invalid or expired. Please request a new password reset link.',     /* phpcs:ignore */
    'password-changed' => 'Your password has been changed, you can now log into your account.',
    'reset-sent'       => 'If there was an account associated with this email address, a new password reset link will be sent.',   /* phpcs:ignore */

    /**
     * VALIDATION RULES
     */
    'validation' => [
        'email-required' => 'You need to provide an email address in order to request a password reset link.',
        'email-invalid'  => 'Please provide a valid email address.',
        'throttled'      => 'You have requested a password reset link too many times. Please try again later.',

        'password-change' => [
            'email-invalid'      => 'Malformed data - invalid email address',
            'email-missing'      => 'Malformed data - missing email address',
            'password-confirmed' => 'You must confirm your new password',
            'password-required'  => 'Provide a new password',
            'password-min'       => 'Your new password is too short. Minimum characters required: ' . Constants::PASSWORD_MIN_LENGTH,   /* phpcs:ignore */
            'token-missing'      => 'Malformed data - missing token',
        ],
    ],
];
