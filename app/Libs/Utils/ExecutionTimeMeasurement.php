<?php

namespace App\Libs\Utils;

use App\Libs\Messages\ExecutionTimeExceptions;
use Closure;
use Exception;

/*
 |--------------------------------------------------------------------------
 | Execution Time Measurement
 |--------------------------------------------------------------------------
 |
 | This is a simple execution time measurement class for blocks of code and Callables
 |
 | Usage:
 | $timer = new ExecutionTimeMeasurement("Some Timer Description", false);
 | $timer->start()
 |      [... block of code ...]
 | $result = $timer->getResult();
 |
 | The above can be simplified by passing true to the constructor as a second parameter:
 | $timer = new ExecutionTimeMeasurement("Some Timer Description, true);
 |      [... block of code ...]
 | $result = $timer->getResult();
 |
 | When testing Callables, it can be even more simplified to one line
 | $timer = (new ExecutionTimeMeasurement("Some Timer Description, false, function() { <function_call> }))
 */

class ExecutionTimeMeasurement
{
    private float $startTime;
    private float $endTime;

    /**
     * String representation of the end result (formatted message)
     */
    private string $result;

    /**
     * An optional message prepended to the execution time result
     *
     * @var string
     */
    private $message;

    /**
     * Separates the result message from calculated execution time. This will only be used
     * if there was a message provided during initialization
     *
     * @var string
     */
    private $separator = " - ";

    /**
     * Modified immediately in the constructor. If a callback has been passed, this will ignore calling finish();
     */
    private bool $isTestingCallback = false;

    /**
     * Indicates that the timer has been started in some way (manually, by parameter or a Callback).
     * 
     * This will will prevent calling finish() before starting
     */
    private bool $hasStarted = false;

    /**
     * @param   string|null     $message            Optional message to prepend to the execution time result. Both Empty of Null are valid when no message is provided
     * @param   bool            $startImmediately   If true, the initial timestamp will be added without needing to start manually. NOTICE: this parameter is ignored if Callback is tested
     * @param   callable        $callback           Callback used for execution time calculation. Tested Callback will immediately build the result
     */
    public function __construct(string $message = null, bool $startImmediately = false, ?Closure $callback = null)
    {
        $_message = trim($message);
        $this->message = empty($_message) === true ? null : $_message;

        // Sometimes we would want to start the timer in the same place right before
        // __some__ block of code. Passing true allows skipping additional step of
        // calling $timer->start().
        // NOTICE: this works only for regular code, because Closures are tested automatically
        if ($startImmediately === true) {
            $this->start();
        }

        // If we have a callback function passed to the constructor,
        // we have to immediately go to results and ignore further finish() calls
        if ($callback !== null) {
            // When testing Closures, we have to start immediately anyway as we don't need to call
            // the Start manually
            $this->start();

            // Execute the passed callback to measure it's execution time and immediately call for results
            $callback();
            $this->getResult();

            // Setting this after calling getResult() allows skipping immediately to finish(), __then__
            // disallowing further calls. 
            $this->isTestingCallback = true;
        }
    }

    /**
     * Returns the current timestamp in microseconds
     */
    private function getCurrentTime(): float
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float) $usec + (float) $sec);
    }

    /**
     * Formats the output to display the results in microseconds, then milliseconds and finally seconds
     * if the execution took long enough.
     * 
     * This function was extracted from Laravel DebugBar
     *
     * @param   float  $timeResult  Numeric result (time execution)
     */
    private function formatResult(float $timeResult): string
    {
        if ($timeResult < 0.001) {
            return round($timeResult * 1000000) . 'μs';
        } elseif ($timeResult < 1) {
            return round($timeResult * 1000, 2) . 'ms';
        }

        return round($timeResult, 2) . 's';
    }

    /**
     * Sets a starting point for the execution time counter. Use this if $startImmediately
     * was omitted during initialization.
     * 
     * This function will immediately skip to getResult() if a Callback was provided during the initialization.
     * 
     * Calling this function before start() is not allowed
     */
    public function start(): void
    {
        $this->hasStarted = true;
        $this->startTime = $this->getCurrentTime();
    }

    /**
     * "Stops" the execution time counter and builds a formatted time result.
     * 
     * Throws an exception when called before start()
     */
    private function finish(): void
    {
        // Since we should not be able to call this function before start()
        // we have to throw an exception, because this will yield incorrect results (no startTime initialized)
        if ($this->hasStarted === false) {
            // Append the timer message to the thrown exception, if any
            $message = ExecutionTimeExceptions::$FINISHED_BEFORE_STARTING . ($this->message != null ? " ({$this->message})" : "");

            throw new Exception($message);
        }

        $this->hasFinished = true;
        $this->endTime = $this->getCurrentTime() - $this->startTime;

        // If there was no message, we will only output the Time Result
        // If the message is present, we will prepend the provided message with a separator
        $this->result = ($this->message === null)
            ? $this->formatResult($this->endTime)
            : $this->message . $this->separator . $this->formatResult($this->endTime);
    }

    /**
     * Returns the formatted execution time result with prepended optional message built by finish() method.
     *
     * If we had a Callback function passed to the constructor, we can't call finish() method since
     * it already had execution time calculated from the very beginning.
     * 
     * @throws \Exception
     */
    public function getResult(): string
    {
        if ($this->isTestingCallback === false) {
            try {
                $this->finish();
            } catch (\Exception $exception) {
                throw $exception;
            }
        }

        return $this->result;
    }

    /**
     * Returns the value of __hasStarted__ state
     */
    public function getState(): bool
    {
        return $this->hasStarted;
    }

    /**
     * Returns this timer's message (custom description or name provided during the initialization)
     */
    public function getMessage(): string|null
    {
        return $this->message;
    }

    /**
     * If there is a Callback passed to this timer, this function returns true
     */
    public function isTestingCallback(): bool
    {
        return $this->isTestingCallback;
    }
}
