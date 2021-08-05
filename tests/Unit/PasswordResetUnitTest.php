<?php

namespace Tests\Unit;

use App\Models\PasswordReset;
use App\Models\User;
use App\Rules\Throttle;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetUnitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Throttle::Clear("throttle-password-reset");
    }
    /**
     * Tests if a password reset token can be created for the provided email
     * 
     * This will also check if new tokens are not expired
     */
    public function test_can_create_password_reset_tokens(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);

        $this->assertDatabaseHas("password_resets", ["token" => $token->token]);
        $this->assertFalse($token->isExpired);
    }

    /**
     * Tests if an expired token can be created
     *
     * This is only for the Factory and further tests purposes
     */
    public function test_can_create_expired_token(): void
    {
        $token = PasswordReset::factory()->expired()->create(["email" => $this->user->email]);
        $this->assertTrue($token->isExpired);

        $tokenStatus = PasswordReset::IsValid($token->token, $token->email);
        $this->assertFalse($tokenStatus);
    }

    /** Tests if token validation can properly detect valid/invalid tokens */
    public function test_can_validate_tokens(): void
    {
        $validToken = PasswordReset::GenerateAndInsert($this->user->email);

        $tokens = [
            ["fake_code", $validToken->email],
            ["", $validToken->email],
            ["fake_code", ""],
            ["", ""],
            [$validToken->token, $this->fakeEmail],
            [$validToken->token, ""],
        ];

        for ($i = 0; $i < count($tokens); $i++) {
            $this->assertFalse(PasswordReset::IsValid($tokens[$i][0], $tokens[$i][1]), "Testing: (code) {$tokens[$i][0]} / (email) {$tokens[$i][1]}");
        }

        $this->assertTrue(PasswordReset::IsValid($validToken->token, $validToken->email), "{$validToken->token} {$validToken->email}");
    }

    /** Tests if the PasswordReset model does not generate and insert a password reset token for fake users */
    public function test_cant_create_tokens_for_non_existing_users(): void
    {
        PasswordReset::GenerateAndInsert($this->fakeEmail);
        $this->assertDatabaseMissing("password_resets", ["email" => $this->fakeEmail]);
    }

    /** Tests if user's password can be updated */
    public function test_can_change_users_password(): void
    {
        $newPassword = "NewpAsswORd1!";
        User::UpdatePassword($this->user->email, $newPassword);
        $user = User::find($this->user->id);
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /** Tests if the created password reset token can be identified and deleted */
    public function test_can_delete_existing_token(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        PasswordReset::DeleteToken($token->token, $token->email);

        $this->assertDatabaseMissing("password_resets", ["token" => $token->token]);
    }
}
