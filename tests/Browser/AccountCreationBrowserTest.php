<?php

namespace Tests\Browser;

use App\Libs\Utils\NamedRoute;
use App\Models\User;
use App\Models\VerificationToken;
use App\Providers\RouteServiceProvider;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AccountCreationBrowserTest extends DuskTestCase
{
    /**
     * Tests if the unauthenticated user can click Sign Up link on the HOME page and sees the account creation form
     *
     * @group account-creation
     */
    public function testUnauthenticatedUserCanNavigateToAccountCreation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click('@link-signup')
                ->assertSee(trans('forms.account-creation.form-header'));
        });
    }

    /**
     * Tests if authenticated user gets redirected to the HOME page
     * and does not see the registration link
     *
     * @group account-creation
     */
    public function testAuthenticatedUserCantVisitAccountCreationPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit(route(NamedRoute::GET_ACCOUNT_CREATION_INDEX))
                ->assertRouteIs(RouteServiceProvider::HOME)
                ->assertNotPresent('@link-signup');
            /** Check if the signup link is not visible in the same test */
        });
    }

    /**
     * Tests if the user can create a new account and can not login (requires verification)
     *
     * @group account-creation
     */
    public function testUserCanCreateANewAccount(): void
    {
        $this->browse(function (Browser $browser) {
            $password = 'SomePassword1!';

            $browser->assertGuest()
                ->visit(route(RouteServiceProvider::HOME))
                ->click('@link-signup')
                ->type('email', $this->fakeEmail)
                ->type('email_confirmation', $this->fakeEmail)
                ->type('password', $password)
                ->type('password_confirmation', $password)
                ->click('@button-register')
                ->assertSee(trans('account_create.created'));

            $browser->visit(route(NamedRoute::GET_SESSION_INDEX))
                ->type('email', $this->fakeEmail)
                ->type('password', $password)
                ->click('@button-login')
                ->assertSee(trans('login.unverified'));
        });
    }

    /**
     * Tests if the login reminder is present on the page. When the user
     * visits this page of refreshes after the verification, a message will show up
     * telling the user to try to login.
     *
     * We are visiting the results link right away to force the regular message.
     *
     * @group account-creation
     */
    public function testUserCanVisitAccountVerificationResultsPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_ACCOUNT_CREATION_STATUS))
                ->assertSee(trans('account_create.verified-reminder'));
        });
    }

    /**
     * Tests if after verifying his account, the user will see a success message
     * and after visiting the same page for the second time, he will see a different
     * message, testing the flash
     *
     * This will also test if the user got verified correctly
     *
     * @group account-creation
     */
    public function testTestUserCanVerifyAccountAndSeeMessages(): void
    {
        $this->browse(function (Browser $browser) {
            User::createNew(['email' => $this->fakeEmail, 'password' => 'FakePassword1!']);

            $token = VerificationToken::where('email', $this->fakeEmail)->first();
            $this->assertNotNull($token);

            $browser->assertGuest()
                ->visit(
                    route(NamedRoute::GET_ACCOUNT_CREATION_VERIFY, ['token' => $token->token, 'email' => $token->email])
                )
                ->assertSee(trans('account_create.verified'))
                ->refresh()
                ->assertSee(trans('account_create.verified-reminder'));

            $this->assertTrue($token->user->isVerified);
        });
    }
}
