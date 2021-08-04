<?php

namespace Tests\Route;

use App\Libs\Utils\NamedRoute;
use Tests\TestCase;

class IndexRouteTest extends TestCase
{
    /** Test if index route returns any errors for unauthenticated users */
    public function test_index_is_ok_for_unauthenticated_user(): void
    {
        $this->get(route(NamedRoute::GET_INDEX))
            ->assertOk()
            ->assertSee(trans("links.index.login"));
    }

    /**
     * Tests if index route returns any errors for authenticated users.
     *
     * This checks if the @auth directive works as intended
     */
    public function test_index_is_ok_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(NamedRoute::GET_INDEX))
            ->assertOk()
            ->assertSee(trans("links.index.logout"));
    }
}
