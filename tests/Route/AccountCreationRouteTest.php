<?php

namespace Tests\Route;

use App\Libs\Utils\RouteNames;
use App\Models\VerificationToken;
use Tests\TestCase;

class AccountCreationRouteTest extends TestCase
{
    /** Test if account_creation route returns any errors for unauthenticated users */
    public function test_account_creation_is_ok_for_unauthenticated_user(): void
    {
        $this->assertGuest()
            ->get(route(RouteNames::GET_ACCOUNT_CREATION_INDEX))
            ->assertOk()
            ->assertSee(trans("links.index.login"));
    }

    /**
     * Tests if index route returns any errors for authenticated users.
     *
     * This checks if the @auth directive works as intended
     */
    public function test_account_creation_is_ok_for_authenticated_user(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(RouteNames::GET_ACCOUNT_CREATION_INDEX))
            ->assertOk()
            ->assertSee(trans("links.index.logout"));
    }

    /**
     * Tests if requesting a new account with correct data does not result in an error.
     *
     * Note: only the status is important, we don't care about eventual validation errors
     */
    public function test_requesting_new_account_is_available(): void
    {
        $this->followingRedirects()
            ->post(
                route(RouteNames::POST_ACCOUNT_CREATION_STORE),
                [
                    "email" => $this->fakeEmail,
                    "email_confirmation" => $this->fakeEmail,
                    "password" => "SomePassword1!",
                    "password_confirmation" => "SomePassword1!",
                ]
            )
            ->assertOk();
    }

    /** Tests if the "status" page does not result in an error for unauthenticated users */
    public function test_account_creation_results_page_is_ok_for_unauthenticated_user(): void
    {
        $this->assertGuest()
            ->get(route(RouteNames::GET_ACCOUNT_CREATION_STATUS))
            ->assertOk();
    }

    /**
     * Tests if the "status" page does not result in an error for unauthenticated users.
     *
     * This should result in a redirection to the HOME page, so we can just follow the redirection
     * and check if the status was still OK.
     *
     * Authenticated users should not be visiting this page anyway
     */
    public function test_account_creation_results_page_is_ok_for_authenticated_user(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->get(route(RouteNames::GET_ACCOUNT_CREATION_STATUS))
            ->assertOk();
    }

    /**
     * Tests if the Account Verification route does not result in an error with correct data
     */
    public function test_account_verification_page_is_ok(): void
    {
        $token = VerificationToken::GenerateAndInsert($this->user->email);

        $this->followingRedirects()
            ->assertGuest()
            ->get(route(RouteNames::GET_ACCOUNT_CREATION_VERIFY, ["token" => $token->token, "email" => $token->email]))
            ->assertOk();
    }

    /**
     * Tests if the Account Verification route does not result in an error when incorrect data is passed
     */
    public function test_account_verification_page_is_ok_with_invalid_data(): void
    {
        $token = VerificationToken::GenerateAndInsert($this->user->email);

        $this->followingRedirects()
            ->assertGuest()
            ->get(route(RouteNames::GET_ACCOUNT_CREATION_VERIFY, ["token" => $token->token, "email" => $this->fakeEmail]))
            ->assertOk();
    }
}
