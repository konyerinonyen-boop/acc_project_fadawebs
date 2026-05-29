<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MemberNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable)
    {
        return ['mail'];
    }

    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome Member')
            ->greeting('Hello '.$notifiable->name)
            ->line('You are now a member.')
            ->line('Thank you for joining us!');
    }
}