<?php

namespace App\Mail;

use App\Models\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailablePasswordReset extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** These parameters contain Password Reset token and the related User data */
    public array $parameters;

    public function __construct(PasswordReset $token)
    {
        $this->parameters = [
            'user' => $token->user,
            'token' => $token,
        ];
    }

    public function build()
    {
        // Consider injecting user's locale in the future(?)
        return $this->to($this->parameters['user']->email)
            ->subject(trans('mailable.password-reset.subject'))
            ->markdown('emails.account.password-reset', $this->parameters);
    }
}
