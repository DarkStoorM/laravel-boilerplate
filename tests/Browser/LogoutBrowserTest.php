<?php

namespace Tests\Browser;

use App\Libs\Utils\NamedRoute;
use App\Providers\RouteServiceProvider;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LogoutBrowserTest extends DuskTestCase
{
    /**
     * This test checks if the route call does not result in an error for unauthenticated user
     *
     * @group logout
     */
    public function testUnauthenticatedUserCanVisitLogoutPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_SESSION_DESTROY))
                ->assertRouteIs(RouteServiceProvider::HOME);
        });
    }

    /**
     * This test checks if the route call does not result in an error for authenticated user
     *
     * @group logout
     */
    public function testAuthenticatedUserCanVisitLogoutPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(NamedRoute::GET_SESSION_DESTROY))
                ->assertRouteIs(RouteServiceProvider::HOME);
        });
    }

    /**
     * Tests if authenticated user can see the Logout link element, click it and
     * get redirected to the Home page.
     *
     * This also covers testing for lack of Logout element presence (functioning session destroy)
     *
     * @group logout
     */
    public function testAuthenticatedUserCanSeeAndClickLogoutLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(RouteServiceProvider::HOME))
                ->assertPresent('@link-logout') /* might not be necessary at all */
                ->click('@link-logout')
                ->assertRouteIs(RouteServiceProvider::HOME)
                ->assertNotPresent('@link-logout');
        });
    }
}
