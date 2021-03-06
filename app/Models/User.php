<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Model Custom Accessors
     |--------------------------------------------------------------------------
     */

    /**
     * Returns the verification status of this user. If this property has a date specified,
     * it means he has been verified.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return $this->email_verified_at !== null;
    }

    /*
     |--------------------------------------------------------------------------
     | Model methods
     |--------------------------------------------------------------------------
     */

    /**
     * Marks this user's email as verified, allowing him to log in
     */
    public function verify(): bool
    {
        return $this->markEmailAsVerified();
    }

    /**
     * Checks if user identified by the $value exists in the database.
     *
     * By default, it performs search under "email" column. Specify a different column
     * can be specified.
     *
     * @param   string  $value   Lookup value to match the user with
     * @param   string  $column  Lookup column - Email address by default
     */
    public static function exists(string $value, string $column = 'email'): bool
    {
        return static::where($column, $value)->exists();
    }

    /**
     * Updates the password of user identified by given email address
     *
     * This assumes the user already exists. Warning, this method should only be called
     * with __validated__ data.
     *
     * @param   string  $email        Validated email address
     * @param   string  $newPassword  Validated password
     */
    public static function updatePassword(string $email, string $newPassword): void
    {
        try {
            static::where('email', $email)->update(['password' => Hash::make($newPassword)]);
        } catch (QueryException $exception) {
            throw $exception->getMessage();
        }
    }

    /**
     * Creates a new User along with the VerificationToken for that user.
     *
     * This should never be called outside the Controller as this is tied to the Form Request
     *
     * @param   array   $userData  Array of user's Email and Password after validation
     *
     * @return  array   An array containing new User and Verification Token
     */
    public static function createNew(array $userData): array|null
    {
        try {
            $user = DB::transaction(function () use ($userData) {
                DB::beginTransaction();

                $user = static::create([
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                ]);

                $token = VerificationToken::generateAndInsert($userData['email']);

                DB::commit();

                return [
                    'user' => $user,
                    'token' => $token,
                ];
            });
        } catch (QueryException $exception) {
            throw $exception->getMessage();
        }

        return $user;
    }
}
