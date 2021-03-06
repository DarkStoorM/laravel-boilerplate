<?php

namespace Tests\Feature;

use App\Libs\Utils\NamedRoute;
use App\Models\User;
use App\Models\VerificationToken;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

class AccountCreationFeatureTest extends TestCase
{
    /** Tests if the account creation page displays correctly for unauthenticated user */
    public function testUnauthenticatedUserCanVisitAccountCreationPage(): void
    {
        $this->assertGuest()
            ->get(route(NamedRoute::GET_ACCOUNT_CREATION_INDEX))
            ->assertSee(trans('forms.account-creation.form-header'));
    }

    /** Tests if authenticated users get redirected to the HOME page and get no error */
    public function testAuthenticatedUserCantVisitAccountCreationPage(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route(NamedRoute::GET_ACCOUNT_CREATION_INDEX))
            ->assertOk();
    }

    /** Tests if unauthenticated user creates a new account successfully */
    public function testUnauthenticatedUserCanRequestNewAccount(): void
    {
        $this->followingRedirects()
            ->assertGuest()
            ->post(
                route(NamedRoute::POST_ACCOUNT_CREATION_STORE),
                [
                    'email' => $this->fakeEmail,
                    'email_confirmation' => $this->fakeEmail,
                    'password' => 'SomePassword1!',
                    'password_confirmation' => 'SomePassword1!',
                ]
            )
            ->assertSee(trans('account_create.created'));

        $this->assertDatabaseHas('users', ['email' => $this->fakeEmail]);
        $this->assertDatabaseHas('verification_tokens', ['email' => $this->fakeEmail]);
    }

    /** Tests if rather than creating a new account from POST request the user gets redirected to the HOME */
    public function testAuthenticatedUserCantRequestNewAccount(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->post(
                route(NamedRoute::POST_ACCOUNT_CREATION_STORE),
                [
                    'email' => $this->fakeEmail,
                    'email_confirmation' => $this->fakeEmail,
                    'password' => 'SomePassword1!',
                    'password_confirmation' => 'SomePassword1!',
                ]
            )
            ->assertLocation(route(RouteServiceProvider::HOME));
    }

    /** Tests if after visiting the results page unauthenticated user sees a notification about logging in */
    public function testUnauthenticatedUserSeesLoginReminder(): void
    {
        $this->assertGuest()
            ->get(route(NamedRoute::GET_ACCOUNT_CREATION_STATUS))
            ->assertSee(trans('account_create.verified-reminder'));
    }

    /** Tests if the view contains flashed message after validating the Verification Token */
    public function testUserSeesSuccessMessageAfterVerifyingNewAccount(): void
    {
        $token = VerificationToken::generateAndInsert($this->user->email);

        $this->followingRedirects()
            ->assertGuest()
            ->get(route(NamedRoute::GET_ACCOUNT_CREATION_VERIFY, ['token' => $token->token, 'email' => $token->email]))
            ->assertSee(trans('account_create.verified'));
    }

    /** Tests if the Account Verification results in an error when the token expires */
    public function testUserCantVerifyAccountWithExpiredToken(): void
    {
        $userData = User::createNew(['email' => $this->fakeEmail, 'password' => 'Password1!']);
        $token = VerificationToken::factory()->expired()->create(['email' => $userData['user']->email]);

        $this->followingRedirects()
            ->assertGuest()
            ->get(route(NamedRoute::GET_ACCOUNT_CREATION_VERIFY, ['token' => $token->token, 'email' => $token->email]))
            ->assertSee(trans('account_create.expired-token'));

        // Make sure that the user does not exist anymore
        $this->assertDatabaseMissing('users', ['email' => $userData['user']->email]);
    }
}
