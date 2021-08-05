<?php

namespace App\Libs;

/**
 * This class declares various Constants, that can be used across the Application,
 * such as certain string lengths or minor settings
 */
class Constants
{
    public const LOGIN_MAXIMUM_ATTEMPTS = 5;
    /** Timeout in seconds the user have to wait if he makes too many login attempts. Normally, it should block the user for 10 minutes */
    public const LOGIN_THROTTLE_TIMEOUT = 600;
    public const PASSWORD_MIN_LENGTH = 8;
    /** TTL in MINUTES for the password reset token to remain active */
    public const PASSWORD_RESET_EXPIRE_TIME = 60;
    /** The maximum amount of time the user can request a password reset link in the given time frame */
    public const PASSWORD_RESET_MAX_REQUEST_ATTEMPTS = 5;
    /** Time in seconds the user has to wait before he can request a password reset link again */
    public const PASSWORD_RESET_THROTTLE_TIMEOUT = 300;
    /** TTL in SECONDS for the request to decay over time */
    public const THROTTLE_DEFAULT_TTL = 3600;
    /** Maximum amount of attempts the user can take in the current TTL window */
    public const THROTTLE_DEFAULT_MAX_ATTEMPTS = 5;
    /** Length of the string for random_bytes */
    public const TOKEN_LENGTH = 100;
    /** TTL in MINUTES for the VERIFICATION token to remain active */
    public const VERIFICATION_TOKEN_EXPIRE_TIME = 60;
}
