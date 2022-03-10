<?php

namespace Tests\Route;

use App\Libs\Utils\NamedRoute;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

class LoginRouteTest extends TestCase
{
    /** Test if login route returns any errors for unauthenticated users */
    public function testLoginIsOkForUnauthenticatedUser(): void
    {
        $this->get(route(NamedRoute::GET_SESSION_INDEX))
            ->assertOk()
            ->assertSee(trans('forms.login.form-header'));
    }

    /** Test if login route returns any errors for authenticated users - should redirect without errors */
    public function testLoginPageIsOkForAuthenticatedUser(): void
    {
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(NamedRoute::GET_SESSION_INDEX))
            ->assertRedirect(route(RouteServiceProvider::HOME));
    }
}
