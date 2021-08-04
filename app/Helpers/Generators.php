<?php

use App\Libs\Constants;

if (!function_exists('generate_random_token')) {
    function generate_random_token(): string
    {
        $token = function () {
            return sha1(random_bytes(Constants::TOKEN_LENGTH));
        };

        return $token() . $token();
    }
}
