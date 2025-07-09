<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.two-factor')
            ->subject('MDRRMO System - Your 2FA Verification Code')
            ->with([
                'two_factor_code' => $this->user->two_factor_code,
                'user_name' => $this->user->full_name,
                'municipality' => $this->user->municipality
            ]);
    }
}
