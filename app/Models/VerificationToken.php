<?php

namespace App\Models;

use App\Libs\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'token', 'expires_at',
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

    /** Checks if this verification reset token is past the expiration date */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at->lessThan(Carbon::now());
    }

    /*
    |--------------------------------------------------------------------------
    | Model Methods
    |--------------------------------------------------------------------------
    */

    /** Creates a new verification token in the database for newly requested account */
    public static function generateAndInsert(string $email): self|null
    {
        return static::create([
            'email' => $email,
            'token' => generate_random_token(),
            'expires_at' => date_create_expiration_timestamp(Constants::VERIFICATION_TOKEN_EXPIRE_TIME),
        ]);
    }

    /**
     * Return the Verification token identified by the token string and associated email address
     *
     * @param   string  $token   Verification token (code generated for the user)
     * @param   string  $email   Email address associated with the searched token
     */
    public static function getToken(string $token, string $email): self|null
    {
        return static::where('token', $token)
            ->where('email', $email)
            ->get()
            ->first();
    }

    /**
     * Checks if the Verification Token is valid (exists and is not expired)
     *
     * @param   string  $token      Verification identification token
     * @param   string  $email      Email address associated with the given token
     */
    public static function isValid(string $token, string $email): bool
    {
        // The given code (token) has to be associated with the provided email
        $token = static::getToken($token, $email);

        // If there was no token, we have to flash an error message and abort further checks
        // This means that the given token could not be associated with the provided email,
        // which can mean that user has modified the link
        if ($token === null) {
            flash_error(trans('account_create.invalid-token'));

            return false;
        }

        // We have the token, so now we have to check if it's expired
        // Since we can't just re-create an account when the token expired (unique constraint),
        // we have to delete the created user and notify the user about it
        if ($token->isExpired === true) {
            $token->user()->delete();
            flash_error(trans('account_create.expired-token'));

            return false;
        }

        return true;
    }
}
