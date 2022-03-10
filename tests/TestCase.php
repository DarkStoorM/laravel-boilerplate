<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // This email will be used for new users that have to be created separately
        // - entering a non-existing email
        $this->fakeEmail = $this->faker->safeEmail();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
