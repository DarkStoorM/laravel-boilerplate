<?php

namespace Tests\Feature;

use Tests\TestCase;

class FlashFeatureTest extends TestCase
{
    /** Checks if the Flash helper can store values in the session after being autoloaded */
    public function test_can_flash_from_helpers(): void
    {
        $message = "test message";

        $flashers = [
            "success-generic" => function () use ($message) {
                flash_success($message);
            },
            "error-generic" => function () use ($message) {
                flash_error($message);
            },
            "generic" => function () use ($message) {
                flash_generic($message);
            },
        ];

        foreach ($flashers as $flasherType => $flasherFunction) {
            $flasherFunction();
            $this->assertEquals($message, session()->get($flasherType), "Testing: " . $flasherType);
        }

        session()->flush();

        // Post-flush also has to be tested...
        // Doesn't matter which flash we test right now
        $this->assertNull(session()->get("generic"));
    }
}
