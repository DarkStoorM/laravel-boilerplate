<?php

namespace Tests\Unit;

use App\Libs\Utils\NamedRoute;
use App\Mail\MailablePasswordReset;
use App\Mail\MailableVerificationToken;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * This class tests sending emails
 */
class MailingUnitTest extends TestCase
{
    /** Tests if emails with account activation link can be sent after creating a new account */
    public function testMailableCanSendAccountVerificationLink(): void
    {
        Mail::fake();

        // Request a new account, which should send a new email to the user
        $this->post(
            route(NamedRoute::POST_ACCOUNT_CREATION_STORE),
            [
                'email' => $this->fakeEmail,
                'email_confirmation' => $this->fakeEmail,
                'password' => 'SomePassword1!',
                'password_confirmation' => 'SomePassword1!',
            ]
        );

        Mail::assertQueued(MailableVerificationToken::class);
    }

    /** Tests if emails with password reset link can be sent after requesting a new password */
    public function testMailableCanSendPasswordResetLinks(): void
    {
        Mail::fake();

        // Request a new password reset link for the existing user
        // This can't use fake email since the user has to exist in the database
        $this->post(
            route(NamedRoute::POST_PASSWORD_RESET_STORE),
            ['email' => $this->user->email]
        );

        Mail::assertQueued(MailablePasswordReset::class);
    }
}
