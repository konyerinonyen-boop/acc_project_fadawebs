<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EventRegistrationNotification extends Notification
{
    use Queueable;

    // Add a public property to store the data
    public $attendance;

    // Accept the attendance data inside your constructor
    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Fixes line 22: Change $notifiable->name to $this->attendance->first_name
        return (new MailMessage)
            ->subject('Event Registration Confirmed')
            ->greeting('Hello ' . $this->attendance->first_name . ',')
            ->line('Your attendance has been successfully recorded.')
            ->line('Thank you for registering!');
    }
}