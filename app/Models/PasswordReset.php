<?php

namespace App\Models;

use App\Libs\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        "email", "token", "expires_at"
    ];

    protected $casts = [
        'expires_at' => 'datetime', /* Casts to Carbon Instance */
    ];

    /** Return the user related to this token */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
    
    /*
    |--------------------------------------------------------------------------
    | Custom Accessors
    |--------------------------------------------------------------------------
    */

    /** Checks if this password reset token is past the expiration date */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at->lessThan(Carbon::now());
    }

    /*
    |--------------------------------------------------------------------------
    | Model Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a new password reset token in the database for the requested email address
     *
     * This will only create a new token if the user exists with provided email address
     */
    public static function GenerateAndInsert(string $email): self|null
    {
        // We don't want to create a password reset token for non-existing users
        if (User::Exists($email) === false) {
            return null;
        }

        return static::create([
            "email" => $email,
            "token" => generate_random_token(),
            "expires_at" => date_create_expiration_timestamp(Constants::PASSWORD_RESET_EXPIRE_TIME)
        ]);
    }

    /**
     * Return the Password Reset token identified by the token string and associated email address
     *
     * @param   string  $token   PasswordReset token (code generated for the user)
     * @param   string  $email   Email address associated with the searched token
     */
    public static function GetToken(string $token, string $email): self|null
    {
        return static::where("token", $token)
            ->where("email", $email)
            ->get()
            ->first();
    }

    /**
     * Checks if the Password Reset Token is valid (exists and is not expired)
     * 
     * @param   string  $token      Password Reset identification code
     * @param   string  $email      Email address associated with the given code
     */
    public static function IsValid(string $token, string $email): bool
    {
        // The given code (token) has to be associated with the provided email
        // We will check both in case users try to "bruteforce" the password resets
        $token = static::GetToken($token, $email);

        // If there was no token, we have to flash an error message and abort further checks
        if ($token === null) {
            return static::InvalidToken();
        }

        // We have the token, so now we have to check if it's expired
        if ($token->isExpired === true) {
            return static::InvalidToken();
        }

        return true;
    }

    /** Shorthand for returning the information back about invalid Password Reset Token. */
    private static function InvalidToken(): bool
    {
        flash_error(trans("password_reset.invalid-token"));
        return false;
    }

    /**
     * Deletes the Password Reset token after successful password change
     *
     * We don't care about the delete status in this case.
     * 
     * @see GetToken scope for parameters
     */
    public static function DeleteToken(string $token, string $email): void
    {
        static::GetToken($token, $email)->delete();
    }
}
