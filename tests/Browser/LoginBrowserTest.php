<?php

namespace Tests\Browser;

use App\Libs\Constants;
use App\Libs\Utils\NamedRoute;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Throttle;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LoginBrowserTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::Clear("throttle-login");
    }

    /**
     * Tests if unauthenticated user doesn't see any errors while visiting the login page.
     * 
     * @group login
     */
    public function test_user_can_visit_login_page_as_unauthenticated(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->assertPresent("@button-login");
        });
    }

    /**
     * Tests if the 'auth' middleware redirects authenticated user back to the HOME.
     *
     * This is to make sure that authenticated user can't log in twice
     * 
     * @group login
     */
    public function test_authenticated_user_gets_redirected_to_home(): void
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
    public function test_user_can_log_in(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click("@link-login")
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->type("email", $this->user->email)
                ->type("password", "password")
                ->click("@button-login")
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
    public function test_user_cant_log_in_with_incorrect_credentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click("@link-login")
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->type("email", $this->fakeEmail)
                ->type("password", "fakepass")
                ->click("@button-login")
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->assertSee(trans("login.failed"));
            // We should still be on the Login route after trying to log in with incorrect credentials
            // We also have to check for this specific error message
        });
    }

    /**
     * Tests if unauthenticated users get throttled if they attempt to log in too many times in given time
     * 
     * @group login
     */
    public function test_user_can_hit_rate_limit(): void
    {
        $this->browse(function (Browser $browser) {
            // Force hitting the rate limit to make the next request get blocked by the Throttle rule1
            for ($i = 0; $i < Constants::LOGIN_MAXIMUM_ATTEMPTS + 1; $i++) {
                $this->followingRedirects()->post(route(NamedRoute::POST_SESSION_STORE), ["email" => $this->fakeEmail, "password" => "password"]);
            }

            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click("@link-login")
                ->assertRouteIs(NamedRoute::GET_SESSION_INDEX)
                ->type("email", $this->fakeEmail)
                ->type("password", "fakepass")
                ->click("@button-login")
                ->assertSee(trans("login.validation.throttled"));
        });
    }

    /**
     * Tests if unverified user stays on the login page, getting an error in return
     *
     * User must be verified in order to proceed
     * 
     * @group login
     */
    public function test_unverified_user_sees_error_message_after_login(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->unverified()->create();

            $browser->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->type("email", $user->email)
                ->type("password", "password")
                ->click("@button-login")
                ->assertSee(trans("login.unverified"));
        });
    }
}
