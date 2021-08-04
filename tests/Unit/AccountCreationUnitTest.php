<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountCreationUnitTest extends TestCase
{
    /**
     * Tests if users can be created (non-factory) and if these users are not verified.
     *
     * This also tests if duplicates are not allowed
     */
    public function test_can_create_accounts_with_no_duplicates(): void
    {
        User::create([
            "email" => "some@email.com",
            "password" => Hash::make("password")
            /** We don't care about password validation here */
        ]);

        $this->assertDatabaseHas("users", ["email" => "some@email.com"]);

        // Besides the default user we should have two users now
        // and the insertion below should not be executed
        $this->assertTrue(User::count() == 2);

        try {
            User::create([
                "email" => "some@email.com",
                "password" => Hash::make("password")
            ]);
        } catch (\Throwable $th) {
        } finally {
            $this->assertTrue(User::count() == 2);
        }
    }

    /**
     * Tests if the Verification Token can be created for given email.
     *
     * This test requires the user to exist as this action is __only__ performed
     * when user creates his account
     */
    public function test_can_create_verification_tokens(): void
    {
        VerificationToken::GenerateAndInsert($this->user->email);

        $this->assertDatabaseHas("verification_tokens", ["email" => $this->user->email]);
    }

    /** Tests if a new User will also have a Verification Token created */
    public function test_can_create_user_with_new_token(): void
    {
        User::CreateNew([
            "email" => "some@fake.mail",
            "password" => "Fakepassword1!"
        ]);

        $this->assertDatabaseHas("verification_tokens", ["email" => "some@fake.mail"]);
    }
}
