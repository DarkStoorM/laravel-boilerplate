<?php

namespace Tests\Feature;

use App\Libs\Utils\ExecutionTimeMeasurement;
use Tests\TestCase;

class ExecutionTimeFeatureTest extends TestCase
{
    /**
     * Tests if Execution Time Measurement can be performed without providing any parameters.
     * 
     * To pass this test, the timer should have no message and should not start immediately
     */
    public function test_canMeasureWithoutParameters(): void
    {
        $timer = new ExecutionTimeMeasurement();

        $this->assertFalse($timer->getState());
        $this->assertNull($timer->getMessage());
    }

    /**
     * Tests if custom timer message can be set and retrieved properly
     */
    public function test_canMeasureWithCustomMessage(): void
    {
        $customMessage = "Test Timer";
        $timer = new ExecutionTimeMeasurement($customMessage);

        $this->assertEquals($customMessage, $timer->getMessage());
    }

    /**
     * Tests if the timer can immediately start when requested after initialization
     */
    public function test_canMeasureWithImmediateStart(): void
    {
        $timer  = new ExecutionTimeMeasurement(null, true);

        $this->assertTrue($timer->getState());
    }

    /**
     * Tests if this class can detect a callback being passed in.
     */
    public function test_canMeasureCallbackExecutionTime(): void
    {
        // Create a new anonymous function for this test, no functionality is needed
        $testClosure = function () {
        };

        $timer = new ExecutionTimeMeasurement("Closure Test", true, $testClosure);

        // We only need to test if the flag was set up upon initialization
        // If the callback was passed, then the timer __should__ set it up
        $this->assertTrue($timer->isTestingCallback());
    }

    /**
     * Tests if we are not actually able to ask for the results when the timer has not started yet.
     */
    public function test_canNotGetResultsBeforeStarting(): void
    {
        // This requires the timer to be initialized without starting
        $timer = new ExecutionTimeMeasurement(null, false);
        $exceptionCaught = false;

        try {
            // Note, we don't care about the returned value
            $timer->getResult();
        } catch (\Exception $exception) {
            // We only care if there was an exception thrown
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught);
    }

    /**
     * Tests if the results are formatted correctly depending on the calculated time values
     * 
     * Warning: this test will sometimes fail due to the sleep not delaying the tests correctly (?).
     * This might not even be needed anyway. Test Restart is needed to avoid this, but it might break
     * Continuous Integration...
     */
    public function test_formatsTheResultsCorrectly(): void
    {
        $delays = [0, 3000, 1050000];
        $tests = ['μs', 'ms', 's'];

        foreach ($delays as $index => $delay) {
            $closure = function () use ($delay) {
                // Force specific sleep time to get the correct results during the simulation
                usleep($delay);
            };

            $timer = new ExecutionTimeMeasurement(null, false, $closure);

            // Each test has to be delayed by a certain amount to force the "expected" result
            $condition = preg_match("/\d+" . $tests[$index] . "/", $timer->getResult());
            $this->assertTrue($condition == 1, "Looking for '{$tests[$index]}' in {$timer->getResult()}. Closure delayed by: {$delay}");
        }
    }
}
