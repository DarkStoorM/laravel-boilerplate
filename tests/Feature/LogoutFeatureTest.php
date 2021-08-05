<?php

namespace Tests\Feature;

use App\Libs\Utils\NamedRoute;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

class LogoutFeatureTest extends TestCase
{
    /** Tests if the session gets destroyed properly and the user gets logged out of the application */
    public function test_can_log_user_out(): void
    {
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(NamedRoute::GET_SESSION_DESTROY));

        $this->assertGuest()
            ->get(route(RouteServiceProvider::HOME))
            ->assertSessionMissing("password_hash_web"); // Session got destroyed
    }
}
