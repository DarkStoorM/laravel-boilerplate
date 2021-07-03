<?php

namespace Tests\Feature;

/**
 * IMPORTANT: This test can be freely removed, this was added only
 * to check if basic migration works and the environment has been
 * configured properly.
 */

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use DatabaseMigrations;

    public function test_can_create_users(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas("users", ["email" => $user->email]);
    }
}
