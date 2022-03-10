<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DatabaseFeatureTest extends TestCase
{
    use DatabaseMigrations;

    /** Tests if the default model can be created during the SetUp */
    public function testCanCreateUsersOnStart(): void
    {
        $this->assertDatabaseHas('users', ['email' => $this->user->email]);
    }

    /** Tests if a model can be created at any point in time by the Factory */
    public function testCanCreateUsersAtRuntime(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }
}
