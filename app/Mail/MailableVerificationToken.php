<?php

namespace App\Mail;

use App\Models\VerificationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailableVerificationToken extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** These parameters contain Verification Token and the related User data */
    public array $parameters;

    public function __construct(array $userData)
    {
        $this->parameters = [
            "user" => $userData["user"],
            "token" => $userData["token"],
        ];
    }

    public function build()
    {
        // Consider injecting user's locale in the future(?)
        return $this->to($this->parameters["user"]->email)
            ->subject(trans("mailable.account-verification.subject", ["app" => config("app.name")]))
            ->markdown("emails.account.verification", $this->parameters);
    }
}
