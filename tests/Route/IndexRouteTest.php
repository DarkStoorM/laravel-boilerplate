<?php

namespace Tests\Route;

use App\Libs\Utils\NamedRoute;
use Tests\TestCase;

class IndexRouteTest extends TestCase
{
    /** Test if index route returns any errors for unauthenticated users */
    public function testIndexIsOkForUnauthenticatedUser(): void
    {
        $this->get(route(NamedRoute::GET_INDEX))
            ->assertOk()
            ->assertSee(trans('links.index.login'));
    }

    /**
     * Tests if index route returns any errors for authenticated users.
     *
     * This checks if the @auth directive works as intended
     */
    public function testIndexIsOkForAuthenticatedUser(): void
    {
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(NamedRoute::GET_INDEX))
            ->assertOk()
            ->assertSee(trans('links.index.logout'));
    }
}
