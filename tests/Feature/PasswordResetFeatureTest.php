<?php

namespace Tests\Feature;

use App\Libs\Constants;
use App\Libs\Utils\NamedRoute;
use App\Models\PasswordReset;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Throttle;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetFeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::Clear("throttle-password-reset");
    }

    /** Tests if the controller will store a new token for the provided user email */
    public function test_can_send_password_reset_links(): void
    {
        $this->assertGuest()
            ->post(route(NamedRoute::POST_PASSWORD_RESET_STORE), ["email" => $this->user->email]);
        $this->assertDatabaseHas("password_resets", ["email" => $this->user->email]);
    }

    /** Tests if the controller will not allow requesting a new password reset link if invalid email was provided */
    public function test_cant_send_password_reset_links_with_invalid_email(): void
    {
        $this->assertGuest()
            ->post(route(NamedRoute::POST_PASSWORD_RESET_STORE), ["email" => "invalid email"])
            ->assertSessionHasErrors();
        $this->assertDatabaseMissing("password_resets", ["email" => "invalid email"]);
    }


    /**
     * Tests if too many password reset request attempts in a short period of time
     * blocks the user from further requests even if the next attempt contains valid data
     */
    public function test_can_throttle_password_reset_requests(): void
    {
        for ($i = 0; $i < Constants::PASSWORD_RESET_MAX_REQUEST_ATTEMPTS + 1; $i++) {
            $this->followingRedirects()->post(route(NamedRoute::POST_PASSWORD_RESET_STORE), ["email" => "fake@mail.com"]);
        }

        // Just make sure that the token was not created for the user that does not exist
        $this->assertDatabaseMissing("password_resets", ["email" => "fake@mail.com"]);

        // This request should be throttled
        $this->post(route(NamedRoute::POST_PASSWORD_RESET_STORE), ["email" => $this->user->email])
            ->assertRedirect(route(NamedRoute::GET_PASSWORD_RESET_INDEX));
        $this->assertDatabaseMissing("password_resets", ["email" => $this->user->email]);

        // Clear the throttle from this test
        //Throttle::Clear("throttle-password-reset");
    }

    /** Tests if the user can see a password change form when he visits a valid link (the token is still available to use) */
    public function test_user_gets_to_change_password_with_valid_token(): void
    {
        // We have to create a valid token for this user to test if the password change form will show up
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        $this->assertDatabaseHas("password_resets", ["token" => $token->token]);
        $routeParameters = ["token" => $token->token, "email" => $this->user->email];

        $this->assertGuest()
            ->get(route(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN, $routeParameters))
            ->assertRedirect(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_CREATE, $routeParameters));

        // Now that we have tested the redirection from token validation, we can test the link itself
        // We should still be able to see the password change form
        // This is required as we are not directly accessing the below route in the above test
        $this->get(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_CREATE, $routeParameters))
            ->assertSee(trans("forms.password-reset.change-password-header"));
    }

    /**
     * When user "visits" a link where the data has been changed, the user should
     * get an error in return saying that the token is invalid or expired and should
     * not get through the validation.
     */
    public function test_user_will_not_see_password_change_form_with_invalid_token(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        $invalidData = [
            ["token" => $token->token, "email" => "fake@mail.com"],
            ["token" => "fake token", "email" => $token->email],
            ["token" => "fake token", "email" => "fake email"],
        ];

        $this->assertGuest();
        foreach ($invalidData as $token) {
            $this->followingRedirects()
                ->get(route(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN, $token))
                ->assertSee(trans("password_reset.invalid-token"));
        }

        // Now we also have to test the link itself, any invalid token will do
        // This is required as we are not directly accessing the below route in the above test
        $this->followingRedirects()
            ->get(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_CREATE, $token))
            ->assertSee(trans("password_reset.invalid-token"));
    }


    /** Tests if unauthenticated user can submit a new password and sees no errors in the process */
    public function test_authenticated_user_cant_submit_password_change_form(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);

        // It does not matter if we submit correct or incorrect data
        // we only need to know if this user gets redirected either way
        $this->actingAs($this->user)
            ->assertAuthenticatedAs($this->user)
            ->post(
                route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ["token" => $token->token, "email" => $token->email]),
                ["password" => "newpassword", "password_confirmation" => "newpassword"]
            )
            ->assertRedirect(route(RouteServiceProvider::HOME));
    }

    /**
     * Tests if unauthenticated user can submit a password change form
     * and sees no errors in the process of changing his password
     */
    public function test_unauthenticated_user_can_submit_password_change_form(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);

        $this->followingRedirects()
            ->post(
                route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ["token" => $token->token, "email" => $token->email]),
                [
                    "password" => "newPassword1!",
                    "password_confirmation" => "newPassword1!",
                    "token" => $token->token, /* we have to add hidden inputs */
                    "email" => $token->email, /* to validate this user again */
                ]
            )->assertOk();

        // User's password should be changed now
        $user = User::find($this->user->id);

        $this->assertTrue(Hash::check("newPassword1!", $user->password));
    }

    /** Tests if the user will encounter validation errors if the password did not pass the validation */
    public function test_user_cant_change_password_with_invalid_data(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        $passwordCases = [
            ["", ""],
            ["pass", "pass"],
            ["pass", ""],
            ["", "pass"],
            ["newpassword", ""],
            ["", "newpassword"],
            ["newPassword", "newPassword"],
            ["someNewPassword1", "someNewPassword1"],
        ];

        foreach ($passwordCases as $password) {
            $this->post(
                route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ["token" => $token->token, "email" => $token->email]),
                [
                    "password" => $password[0],
                    "password_confirmation" => $password[1],
                    "token" => $token->token,
                    "email" => $token->email,
                ]
            )->assertSessionHasErrors();
        }
    }

    /**
     * This tests if user gets stopped by the Controller after trying to post new password with a different token
     *
     * Changing the email in the request should send the user back to the token invalidation
     */
    public function test_user_cant_change_password_with_malformed_data(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);

        // Since we are dealing with a regular flash, we are not checking for SessionErrors
        $this->followingRedirects()
            ->assertGuest()
            ->post(
                route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ["token" => $token->token, "email" => $token->email]),
                [
                    "password" => "newPassword1!",
                    "password_confirmation" => "newPassword1!",
                    "token" => $token->token,
                    "email" => "some@different.mail",
                ]
            );

        // The token should still be there, which means we did not change our password
        $this->assertDatabaseHas("password_resets", ["token" => $token->token]);

        // We have to retrieve the user to grab a fresh copy
        $user = User::find($this->user->id);
        $this->assertTrue(Hash::check("password", $user->password));

        // Now let's say we do the same, but with the correct data
        // the password is now changed and the token does not exist
        $this->followingRedirects()
            ->post(
                route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ["token" => $token->token, "email" => $token->email]),
                [
                    "password" => "newPassword1!",
                    "password_confirmation" => "newPassword1!",
                    "token" => $token->token,
                    "email" => $this->user->email,
                ]
            );

        // The token should not be there anymore
        $this->assertDatabaseMissing("password_resets", ["token" => $token->token]);

        // We have to grab a fresh user again
        $user = User::find($this->user->id);
        $this->assertTrue(Hash::check("newPassword1!", $user->password));
    }

    /**
     * Tests if the password reset token will be deleted by the Controller
     * after successfully changing the user password.
     *
     * This should also clear the Password Reset Request attempts
     */
    public function test_user_can_change_password_and_invalidate_requested_token(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        $this->assertDatabaseHas("password_resets", ["token" => $token->token]);

        $this->followingRedirects()
            ->post(
                route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ["token" => $token->token, "email" => $token->email]),
                [
                    "password" => "newPassword1!",
                    "password_confirmation" => "newPassword1!",
                    "token" => $token->token,
                    "email" => $token->email,
                ]
            );
        $this->assertDatabaseMissing("password_resets", ["token" => $token->token]);
    }
}
