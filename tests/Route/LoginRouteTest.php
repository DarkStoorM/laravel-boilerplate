<?php

namespace Tests\Route;

use App\Libs\Utils\RouteNames;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

class LoginRouteTest extends TestCase
{
    /** Test if login route returns any errors for unauthenticated users */
    public function test_login_is_ok_for_unauthenticated_user(): void
    {
        $this->get(route(RouteNames::GET_SESSION_INDEX))
            ->assertOk()
            ->assertSee(trans("forms.login.form-header"));
    }

    /** Test if login route returns any errors for authenticated users - should redirect without errors */
    public function test_login_page_is_ok_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(RouteNames::GET_SESSION_INDEX))
            ->assertRedirect(route(RouteServiceProvider::HOME));
    }
}
