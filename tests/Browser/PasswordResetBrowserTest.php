<?php

namespace Tests\Browser;

use App\Libs\Constants;
use App\Libs\Utils\NamedRoute;
use App\Models\PasswordReset;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Throttle;
use Illuminate\Support\Facades\Hash;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class PasswordResetBrowserTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::Clear("throttle-password-reset");
    }

    /**
     * Test if after visiting a password reset route unauthenticated user can see
     * the password reset form instead of getting redirected back
     *
     * @group password-reset
     */
    public function test_unauthenticated_user_can_visit_password_reset_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_INDEX))
                ->assertSee(trans("forms.password-reset.form-header"));
        });
    }

    /**
     * Tests if authenticated user gets redirected by "guest" middleware
     * to the HOME page after visiting the password reset page
     * 
     * @group password-reset
     */
    public function test_authenticated_user_cant_visit_password_reset_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_INDEX))
                ->assertRouteIs(RouteServiceProvider::HOME);
        });
    }

    /**
     * Tests if unauthenticated user can navigate to the password reset page
     * by visiting the HOME page, clicking a Login link, then clicking Forgot Password link
     *
     * This tests if all selectors are in place and they link to appropriate pages
     *
     * @group password_reset
     */
    public function test_unauthenticated_user_can_navigate_to_password_reset_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click("@link-login")
                ->click("@link-forgot-password")
                ->assertRouteIs(NamedRoute::GET_PASSWORD_RESET_INDEX);
        });
    }

    /**
     * Tests if user can navigate to the Password Reset page and request a password reset link.
     *
     * This also tests if the token was created for the email the user has submitted
     *
     * @group password-reset
     */
    public function test_user_can_request_password_reset_link(): void
    {
        $this->browse(function (Browser $browser) {
            // We have covered navigating to the Login screen, we will start straight up send the form
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_INDEX))
                ->type("email", $this->user->email)
                ->click("@button-password-reset-request")
                ->assertSee(trans("password_reset.reset-sent"));

            // We have tom make sure that new token was inserted for this email
            $this->assertDatabaseHas("password_resets", ["email" => $this->user->email]);
        });
    }

    /**
     * Tests if the password reset token will not be created in the database when user provided a fake email.
     *
     * @group password-reset
     */
    public function test_user_cant_request_password_reset_link_for_non_existing_email(): void
    {
        $this->browse(function (Browser $browser) {
            $fakeEmail = $this->fakeEmail;

            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_INDEX))
                ->type("email", $fakeEmail)
                ->click("@button-password-reset-request")
                ->assertSee(trans("password_reset.reset-sent"));

            // We can not see this email in the database
            $this->assertDatabaseMissing("password_resets", ["email" => $fakeEmail]);
        });
    }

    /**
     * Tests if the user can see throttled message on too many attempts and that new requests won't get through
     * 
     * @group password-reset
     */
    public function test_user_cant_make_too_many_password_reset_requests(): void
    {
        // Force throttle
        for ($i = 0; $i < Constants::PASSWORD_RESET_MAX_REQUEST_ATTEMPTS + 1; $i++) {
            $this->followingRedirects()->post(route(NamedRoute::POST_PASSWORD_RESET_STORE), ["email" => $this->fakeEmail]);
        }

        // The following request should return a throttle message
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_INDEX))
                ->type("email", $this->user->email)
                ->click("@button-password-reset-request")
                ->assertSee(trans("password_reset.validation.throttled"));
        });
    }

    /**
     * Tests if visiting a link with valid password reset token will show user the password change form
     * 
     * @group password-reset
     */
    public function test_user_can_see_password_reset_form(): void
    {
        $this->browse(function (Browser $browser) {
            $token = PasswordReset::GenerateAndInsert($this->user->email);
            $this->assertDatabaseHas("password_resets", ["email" => $this->user->email]);

            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN, ["token" => $token->token, "email" => $token->email]))
                ->assertSee(trans("forms.password-reset.change-password-header"));
        });
    }

    /**
     * Tests if visiting the password token validation link with a wrong data (token/email)
     * will show user the "invalid token" error message
     * 
     * @group password-reset
     */
    public function test_user_cant_see_password_change_form_with_invalid_token(): void
    {
        $this->browse(function (Browser $browser) {
            // Let's create some invalid data first
            $token = PasswordReset::GenerateAndInsert($this->user->email);
            $invalidData = [
                ["token" => $token->token, "email" => $this->fakeEmail],
                ["token" => "fake token", "email" => $token->email],
                ["token" => "fake token", "email" => "fake email"],
            ];

            $browser->assertGuest();

            foreach ($invalidData as $token) {
                $browser->visit(route(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN, $token))
                    ->assertSee(trans("password_reset.invalid-token"));
            }
        });
    }

    /**
     * Tests if the user gets redirected to the home page after visiting a
     * valid password reset link
     * 
     * @group password-reset
     */
    public function test_authenticated_user_cant_change_password(): void
    {
        $this->browse(function (Browser $browser) {
            $token = PasswordReset::GenerateAndInsert($this->user->email);
            $this->assertDatabaseHas("password_resets", ["token" => $token->token]);

            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_CREATE, ["token" => $token->token, "email" => $token->email]))
                ->assertRouteIs(RouteServiceProvider::HOME);
        });
    }

    /**
     * Tests if user can change his password after his password reset token has been validated
     *
     * This also covers missing token assertion
     * 
     * @group password-reset
     */
    public function test_user_can_change_password_after_validation_and_invalidates_token(): void
    {
        $this->browse(function (Browser $browser) {
            $newPassword = "someNewPasswoRD1!";
            $this->assertFalse(Hash::check($newPassword, User::first()->password));

            $browser->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->click("@link-forgot-password")
                ->assertRouteIs(NamedRoute::GET_PASSWORD_RESET_INDEX)
                ->type("email", $this->user->email)
                ->click("@button-password-reset-request")
                ->assertSee(trans("password_reset.reset-sent"));

            $token = PasswordReset::first();
            $this->assertNotNull($token);

            $browser->visit(route(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN, ["token" => $token->token, "email" => $token->email]))
                ->assertSee(trans("forms.password-reset.change-password-header"))
                ->type("password", $newPassword)
                ->type("password_confirmation", $newPassword)
                ->click("@button-password-reset-new-password")
                ->assertSee(trans("password_reset.password-changed"));

            $this->assertTrue(Hash::check($newPassword, User::first()->password));
            $this->assertDatabaseMissing("password_resets", ["token" => $token->token]);
        });
    }
}
