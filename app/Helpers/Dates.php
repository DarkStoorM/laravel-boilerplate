<?php

use Carbon\Carbon;

if (!function_exists('date_create_expiration_timestamp')) {
    /**
     * Returns a new Carbon instance with future date (token expiration date).
     *
     * If true was passed as a parameter, this will add negative value
     *
     * @param   float   $timeToExpire  Time in MINUTES for the object to remain active/valid
     * @param   bool    $expired       When true, an expired timestamp will be created
     */
    function date_create_expiration_timestamp(float $timeToExpire, bool $expired = false): Carbon
    {
        return Carbon::now()->addMinutes($timeToExpire * ($expired === true ? -1 : 1));
    }
}
