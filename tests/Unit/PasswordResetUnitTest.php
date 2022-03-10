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
        Throttle::clear('throttle-password-reset');
    }
    /**
     * Tests if a password reset token can be created for the provided email
     *
     * This will also check if new tokens are not expired
     */
    public function testCanCreatePasswordResetTokens(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);

        $this->assertDatabaseHas('password_resets', ['token' => $token->token]);
        $this->assertFalse($token->isExpired);
    }

    /**
     * Tests if an expired token can be created
     *
     * This is only for the Factory and further tests purposes
     */
    public function testCanCreateExpiredToken(): void
    {
        $token = PasswordReset::factory()->expired()->create(['email' => $this->user->email]);
        $this->assertTrue($token->isExpired);

        $tokenStatus = PasswordReset::isValid($token->token, $token->email);
        $this->assertFalse($tokenStatus);
    }

    /** Tests if token validation can properly detect valid/invalid tokens */
    public function testCanValidateTokens(): void
    {
        $validToken = PasswordReset::GenerateAndInsert($this->user->email);

        $tokens = [
            ['fake_code', $validToken->email],
            ['', $validToken->email],
            ['fake_code', ''],
            ['', ''],
            [$validToken->token, $this->fakeEmail],
            [$validToken->token, ''],
        ];

        $tokensCount = count($tokens);
        for ($i = 0; $i < $tokensCount; $i++) {
            $this->assertFalse(
                PasswordReset::isValid(
                    $tokens[$i][0],
                    $tokens[$i][1]
                ),
                "Testing: (code) {$tokens[$i][0]} / (email) {$tokens[$i][1]}"
            );
        }

        $this->assertTrue(
            PasswordReset::isValid(
                $validToken->token,
                $validToken->email
            ),
            "{$validToken->token} {$validToken->email}"
        );
    }

    /** Tests if the PasswordReset model does not generate and insert a password reset token for fake users */
    public function testCantCreateTokensForNonExistingUsers(): void
    {
        PasswordReset::GenerateAndInsert($this->fakeEmail);
        $this->assertDatabaseMissing('password_resets', ['email' => $this->fakeEmail]);
    }

    /** Tests if user's password can be updated */
    public function testCanChangeUsersPassword(): void
    {
        $newPassword = 'NewpAsswORd1!';
        User::updatePassword($this->user->email, $newPassword);

        $user = User::find($this->user->id);
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /** Tests if the created password reset token can be identified and deleted */
    public function testCanDeleteExistingToken(): void
    {
        $token = PasswordReset::GenerateAndInsert($this->user->email);
        PasswordReset::deleteToken($token->token, $token->email);

        $this->assertDatabaseMissing('password_resets', ['token' => $token->token]);
    }
}
