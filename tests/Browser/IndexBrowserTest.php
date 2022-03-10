<?php

namespace Tests\Browser;

use App\Libs\Utils\NamedRoute;
use App\Providers\RouteServiceProvider;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IndexBrowserTest extends DuskTestCase
{
    /**
     * Tests if the user visiting index page doesn't see any errors
     *
     * @group index
     */
    public function testUserCanVisitIndexAsUnauthenticated(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_INDEX))
                ->assertPresent('@link-login');
        });
    }

    /**
     * Tests if authenticated user visiting the index page doesn't see any errors
     *
     * @group index
     */
    public function testUserCanVisitIndexAsAuthenticated(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticated()
                ->visit(route(NamedRoute::GET_INDEX))
                ->assertSeeLink(trans('links.index.logout'));
        });
    }

    /**
     * Tests if unauthenticated user sees an element, which allows logging in.
     *
     * This specifically has to look for Present Element, since not every project can use a __text__ as login element.
     *
     * By checking for a dusk selector presence, we can make sure that any type of login element can be tested: text/image.
     *
     * @group index
     */
    public function testUnauthenticatedUserCanSeeGuestPanel(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->assertPresent('@link-login');
        });
    }

    /**
     * Tests if authenticated users can see a different panel when they are logged in.
     *
     * Refer to the presence check / only the logout link has to be checked.
     *
     * @group index
     */
    public function testAuthenticatedUserCanSeeAuthPanel(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(RouteServiceProvider::HOME))
                ->assertPresent('@link-logout');
        });
    }
}
