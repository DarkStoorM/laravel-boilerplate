<?php

namespace App\Rules;

use App\Libs\Constants;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Throttle implements Rule
{
    /**
     * The throttle key
     */
    protected string $key = 'validation';

    /**
     * The maximum number of attempts a user can perform
     */
    protected int $maxAttempts = 5;

    /**
     * The amount of minutes to restrict the requests by
     */
    protected int $decayInSeconds = 10;

    /**
     * Message the user will see in return when too many attempts were made in a short period of time
     */
    protected string $message = '';

    /**
     * Create a new rule instance.
     *
     * @param string $key
     * @param int    $maxAttempts
     * @param int    $decayInSeconds
     * @param string $message           Custom message presented to the throttled user
     *
     * @return void
     */
    public function __construct($key = 'validation', $maxAttempts = Constants::THROTTLE_DEFAULT_MAX_ATTEMPTS, $decayInSeconds = Constants::THROTTLE_DEFAULT_TTL, string $message = '')
    {
        $this->key = $key;
        $this->maxAttempts = $maxAttempts;
        $this->decayInSeconds = $decayInSeconds;

        // We will use a default throttle message if no $message parameter was provided
        $this->message = empty($message) === true
            ? trans('generic.throttled')
            : $message;
    }

    /**
     * Determine if the validation rule passes
     *
     * @param string $attribute
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        if ($this->hasTooManyAttempts()) {
            return false;
        }

        $this->incrementAttempts();

        return true;
    }

    /** Get the validation error message */
    public function message(): string
    {
        return $this->message;
    }

    /** Determine if the user has too many failed login attempts */
    protected function hasTooManyAttempts(): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey(),
            $this->maxAttempts
        );
    }

    /** Increment the login attempts for the user */
    protected function incrementAttempts(): void
    {
        $this->limiter()->hit(
            $this->throttleKey(),
            $this->decayInSeconds
        );
    }

    /** Get the throttle key for the given request */
    protected function throttleKey(): string
    {
        return $this->key . '|' . $this->request()->ip();
    }

    /** Get the rate limiter instance */
    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    /** Get the current HTTP request */
    protected function request(): Request
    {
        return app(Request::class);
    }

    /**
     * Clears the throttle for current user under specified throttle key
     *
     * @param   string  $key  Throttle key
     */
    public static function clear(string $key): void
    {
        Cache::forget($key . '|' . request()->ip());
    }
}
