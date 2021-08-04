<?php

namespace Tests\Route;

use App\Libs\Utils\RouteNames;
use App\Models\PasswordReset;
use App\Providers\RouteServiceProvider;
use App\Rules\Throttle;
use Tests\TestCase;

class PasswordResetRouteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::Clear("throttle-password-reset");
    }

    /**
     * Tests if the password reset route does not result in a redirect or an error for Guests.
     *
     * Guest middleware is applied to this group, so only for authenticated users it should result in a redirect
     **/
    public function test_password_reset_is_ok_for_unauthenticated_user(): void
    {
        $this->assertGuest()
            ->get(route(RouteNames::GET_PASSWORD_RESET_INDEX))
            ->assertOk();
    }

    /**
     * Tests if the password reset route does not result in an error for authenticated users.
     *
     * Since this will result in a redirect for Authenticated users, we have to test if there
     * will be no errors after the redirection happens - this require us to follow redirects
     */
    public function test_password_reset_is_ok_for_authenticated_user(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(RouteNames::GET_PASSWORD_RESET_INDEX))
            ->assertOk();
    }

    /** Tests if submitting a form will properly redirect back without returning any errors */
    public function test_password_reset_is_ok_after_submission(): void
    {
        $this->assertGuest()
            ->post(route(RouteNames::POST_PASSWORD_RESET_STORE), ["email" => $this->user->email])
            ->assertSessionHasNoErrors();

        // Let's also try incorrect data just in case
        $this->post(route(RouteNames::POST_PASSWORD_RESET_STORE), ["email" => "asdf"])
            ->assertSessionHasErrors();
    }

    /** Tests if the "results" page of the password reset does not return any errors */
    public function test_password_reset_results_route_is_ok(): void
    {
        $this->assertGuest()
            ->get(route(RouteNames::GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT))
            ->assertOk();
    }

    /** Tests if the authenticated user gets redirected properly while visiting the password reset results page */
    public function test_password_reset_results_route_is_ok_for_authenticated_users(): void
    {
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(RouteNames::GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT))
            ->assertRedirect(route(RouteServiceProvider::HOME));
    }

    /** Tests if password change route does not error out when the user has a valid token */
    public function test_password_change_route_is_ok(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        $this->get(route(RouteNames::GET_PASSWORD_RESET_CHANGE_CREATE, ["token" => $token->token, "email" => $token->email]))
            ->assertOk();
    }

    /**
     * Tests if the password change route does not error out for authenticated users
     *
     * Even though this situation should never happen, but assuming, someone that
     * is logged in, but also visits the password reset with a valid token, should
     * still get redirected back
     */
    public function test_password_change_route_is_ok_for_authenticated_user(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        $this->followingRedirects()
            ->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(RouteNames::GET_PASSWORD_RESET_CHANGE_CREATE, ["token" => $token->token, "email" => $token->email]))
            ->assertOk();
    }
}
