<?php

namespace Tests\Route;

use App\Libs\Utils\NamedRoute;
use Tests\TestCase;

class LogoutRouteTest extends TestCase
{
    /** Tests if Logout route returns any errors for unauthenticated users */
    public function testLogoutIsOkForUnauthenticatedUser(): void
    {
        $this->followingRedirects()
            ->get(route(NamedRoute::GET_SESSION_DESTROY))
            ->assertOk();
    }

    /**
     * Tests if Logout route returns any errors for authenticated users.
     *
     * Authentication does not really matter, because the user will get redirected to
     * the HOME route anyway.
     */
    public function testLogoutAuthenticatedUserGetsRedirectedToHome(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(NamedRoute::GET_SESSION_DESTROY))->assertOk();
    }
}
