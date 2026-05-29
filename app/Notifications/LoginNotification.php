<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LoginNotification extends Notification
{
    public function via(object $notifiable)
    {
        return ['mail'];
    }

    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject('Login Alert')
            ->line('You have logged in successfully.');
    }
}