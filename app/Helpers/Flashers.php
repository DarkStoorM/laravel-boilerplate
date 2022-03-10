<?php

if (!function_exists('flash_success')) {
    /**
     * This Flasher should be used to display a success message in a container actually
     * indicating, that we got a success message in the result of some action - a
     * container e.g. in a green box or with a green accent
     *
     * @param   string  $successMessage  Flashed message
     */
    function flash_success(string $successMessage): void
    {
        session()->flash('success-generic', $successMessage);
    }
}

if (!function_exists('flash_error')) {
    /**
     * This Flasher should be used to display a success message in a container actually
     * indicating, that we got a success message in the result of some action - a
     * container e.g. in a green box or with a green accent
     *
     * @param   string  $errorMessage  Flashed Error message
     */
    function flash_error(string $errorMessage): void
    {
        session()->flash('error-generic', $errorMessage);
    }
}

if (!function_exists('flash_generic')) {
    /**
     * This is a generic flasher, that should be used in regular boxes, neither as a success or
     * a error message.
     *
     * @param   string  $message  Flashed message
     */
    function flash_generic(string $message): void
    {
        session()->flash('generic', $message);
    }
}
