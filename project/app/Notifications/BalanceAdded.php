<?php
// app/Notifications/BalanceAdded.php


namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BalanceAdded extends Notification
{
    protected $amount;
    protected $currency;

    public function __construct($amount, $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Saldo agregado a su cuenta')
            ->line('Estimado ' . $notifiable->name)
            ->line('Se ha agregado ' . $this->amount . ' ' . $this->currency . ' a su cuenta.')
            ->line('Su nuevo saldo es: ' . $notifiable->balance . ' ' . $this->currency);
    }
}