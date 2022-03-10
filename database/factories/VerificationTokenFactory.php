<?php

namespace Database\Factories;

use App\Libs\Constants;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VerificationToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $email;

        return [
            'email' => $email,
            'token' => generate_random_token(),
            'expires_at' => date_create_expiration_timestamp(Constants::VERIFICATION_TOKEN_EXPIRE_TIME),
        ];
    }

    /** Indicate that the model's token has expired. */
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => date_create_expiration_timestamp(Constants::VERIFICATION_TOKEN_EXPIRE_TIME, true),
            ];
        });
    }
}
