<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = url("/verify-email/" . $this->user->verification_token);

        return (new MailMessage)
            ->subject('MDRRMO System - Verify Your Email Address')
            ->greeting('Hello ' . $this->user->full_name . '!')
            ->line('Welcome to the MDRRMO Accident Reporting System for ' . $this->user->municipality . '.')
            ->line('Please click the button below to verify your email address:')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, please contact your system administrator.')
            ->line('This verification link will expire in 24 hours.')
            ->salutation('MDRRMO System Team');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
