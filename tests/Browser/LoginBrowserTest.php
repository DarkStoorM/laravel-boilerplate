<?php

namespace Tests\Browser;

use App\Libs\Constants;
use App\Libs\Utils\NamedRoute;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Throttle;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginBrowserTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::clear('throttle-login');
    }

    /**
     * Tests if unauthenticated user doesn't see any errors while visiting the login page.
     *
     * @group login
     */
    public function testUserCanVisitLoginPageAsUnauthenticated(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->assertPresent('@button-login');
        });
    }

    /**
     * Tests if the 'auth' middleware redirects authenticated user back to the HOME.
     *
     * This is to make sure that authenticated user can't log in twice
     *
     * @group login
     */
    public function testAuthenticatedUserGetsRedirectedToHome(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->assertRouteIs(RouteServiceProvider::HOME);
        });
    }

    /**
     * Tests if unauthenticated user can manually log in and doesn't see any errors
     *
     * @group login
     */
    public function testUserCanLogIn(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click('@link-login')
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->type('email', $this->user->email)
                ->type('password', 'password')
                ->click('@button-login')
                ->assertAuthenticated();
        });
    }

    /**
     * Tests if unauthenticated user can't get through authentication with incorrect credentials.
     *
     * This makes sure Auth::attempt works as intended
     *
     * @group login
     */
    public function testUserCantLogInWithIncorrectCredentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click('@link-login')
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->type('email', $this->fakeEmail)
                ->type('password', 'fakepass')
                ->click('@button-login')
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->assertSee(trans('login.failed'));
            // We should still be on the Login route after trying to log in with incorrect credentials
            // We also have to check for this specific error message
        });
    }

    /**
     * Tests if unauthenticated users get throttled if they attempt to log in too many times in given time
     *
     * @group login
     */
    public function testUserCanHitRateLimit(): void
    {
        $this->browse(function (Browser $browser) {
            // Force hitting the rate limit to make the next request get blocked by the Throttle rule1
            for ($i = 0; $i < Constants::LOGIN_MAXIMUM_ATTEMPTS + 1; $i++) {
                $this->followingRedirects()->post(
                    route(NamedRoute::POST_SESSION_STORE),
                    ['email' => $this->fakeEmail, 'password' => 'password']
                );
            }

            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click('@link-login')
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->type('email', $this->fakeEmail)
                ->type('password', 'fakepass')
                ->click('@button-login')
                ->assertSee(trans('login.validation.throttled'));
        });
    }

    /**
     * Tests if unverified user stays on the login page, getting an error in return
     *
     * User must be verified in order to proceed
     *
     * @group login
     */
    public function testUnverifiedUserSeesErrorMessageAfterLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->unverified()->create();

            $browser->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->type('email', $user->email)
                ->type('password', 'password')
                ->click('@button-login')
                ->assertSee(trans('login.unverified'));
        });
    }
}
