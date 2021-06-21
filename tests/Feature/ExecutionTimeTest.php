<?php

namespace Tests\Feature;

use App\Libs\Utils\ExecutionTimeMeasurement;
use Tests\TestCase;
use Illuminate\Support\Str;

class ExecutionTimeTest extends TestCase
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
     * 
     * Also checks if the message of this timer has a (Callback) label appended to it.
     */
    public function test_canMeasureCallbackExecutionTime(): void
    {
        // Create a new anonymous function for this test
        $testClosure = function () {
            return;
        };

        $timer = new ExecutionTimeMeasurement("Closure Test", true, $testClosure);
        $this->assertTrue($timer->isTestingCallback());

        // Callbacks have results immediately available, so we have to check if there is a label prepended
        $this->assertTrue(Str::contains($timer->getResult(), "(Callback)"));
    }

    public function test_canNotGetResultsBeforeStarting(): void
    {
    }
}
