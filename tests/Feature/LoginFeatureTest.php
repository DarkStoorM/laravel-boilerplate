<?php

namespace Tests\Feature;

use App\Libs\Constants;
use App\Libs\Utils\NamedRoute;
use App\Models\User;
use App\Rules\Throttle;
use Tests\TestCase;

class LoginFeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::clear('throttle-login');
    }

    /**
     * This test should show, that after providing correct credentials,
     * the user gets redirected to the HOME route and can see some Authenticated part (logout link)
     */
    public function testUserCanLogIn(): void
    {
        $this->followingRedirects()
            ->post(route(NamedRoute::POST_SESSION_STORE), ['email' => $this->user->email, 'password' => 'password'])
            ->assertSee(trans('links.index.logout'));
    }

    /**
     * This test should show, that after providing incorrect credentials,
     * the user does not get redirected to the HOME route, but stays in the Login Route
     * and sees Authentication Failure message.
     */
    public function testUserCantLoginWithIncorrectCredentials(): void
    {
        $this->followingRedirects()
            ->post(route(NamedRoute::POST_SESSION_STORE), ['email' => $this->fakeEmail, 'password' => 'password'])
            ->assertSee(trans('login.failed'));
    }

    /**
     * This test shows that, that after trying to log in too many times
     * with incorrect credentials, the user will see a Throttle message.
     */
    public function testUserGetsThrottledOnTooManyAttempts(): void
    {
        for ($i = 0; $i < Constants::LOGIN_MAXIMUM_ATTEMPTS + 1; $i++) {
            $this->followingRedirects()->post(
                route(NamedRoute::POST_SESSION_STORE),
                ['email' => $this->fakeEmail, 'password' => 'password']
            );
        }

        // The next message should be throttled, so the user should see "too many attempts" message
        $this->followingRedirects()
            ->post(route(NamedRoute::POST_SESSION_STORE), ['email' => $this->fakeEmail, 'password' => 'password'])
            ->assertSee(trans('login.validation.throttled'));

        // The user should be able to log in after some time, so let's also test the cache clear
        // we will just use incorrect credentials to check the error message
        Throttle::clear('throttle-login');

        $this->followingRedirects()
            ->post(route(NamedRoute::POST_SESSION_STORE), ['email' => $this->fakeEmail, 'password' => 'password'])
            ->assertSee(trans('login.failed'));
    }

    /** Tests if new user will see an error message after trying to log in without verifying his email */
    public function testUnverifiedUserCantLogin(): void
    {
        // We have to create an unverified user for this test
        $user = User::factory()->unverified()->create();

        $this->followingRedirects()
            ->post(route(NamedRoute::POST_SESSION_STORE), ['email' => $user->email, 'password' => 'password'])
            ->assertSee(trans('login.unverified'));
    }
}
