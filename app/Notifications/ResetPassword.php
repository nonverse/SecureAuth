<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var string
     */
    private string $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from('no-reply@auth.nonverse.net', 'Nonverse Auth')
            ->subject('Reset password')
            ->markdown('mail.reset-password', [
                'name' => $this->user->name_first,
                'url' => $this->url
            ]);
    }
}
