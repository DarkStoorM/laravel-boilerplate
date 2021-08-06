<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\MailablePasswordReset;
use App\Mail\MailableVerificationToken;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * This class tests the Mailable content - if the data is rendered correctly in the view
 */
class MailableUnitTest extends TestCase
{
    use WithFaker;

    /** Tests if the generated token and user's email is present in the mailable content */
    public function test_mailable_password_resets(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);

        $mailable = new MailablePasswordReset($token);
        $mailable->assertSeeInHtml($token->token);
        $mailable->assertSeeInHtml($token->email);
    }

    /** Tests if the generated token for a new user is present in the mailable content */
    public function test_mailable_account_verification_tokens(): void
    {
        // We have to create a new user in order to test this Mailable
        // We will not be using a Factory here since we basically need a new Account that we want to verify
        // Keep in mind this method should only be available in the Controller or testing, since the data
        // comes in __pre-validated__ 
        $userData = User::CreateNew(["email" => $this->faker->safeEmail(), "password" => "Password1!"]);

        $mailable = new MailableVerificationToken($userData);
        $mailable->assertSeeInHtml($userData["user"]->email);
        $mailable->assertSeeInHtml($userData["token"]->token);
    }
}
