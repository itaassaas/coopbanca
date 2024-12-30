<?php
// app/Notifications/AdminBalanceNotification.php


namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminBalanceNotification extends Notification
{
    use Queueable;
    
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->data['title'])
            ->line($this->data['body'])
            ->line('Monto: ' . $this->data['amount'])
            ->line('Usuario: ' . $this->data['user']);
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}