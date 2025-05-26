<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(config('app.frontend_url').'/reset-password?token='.$this->token.'&email='.$notifiable->email);

        return (new MailMessage)
                    ->subject('Réinitialisation de votre mot de passe')
                    ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
                    ->action('Réinitialiser le mot de passe', $resetUrl)
                    ->line('Si vous n’avez pas demandé cette réinitialisation, aucune action n’est requise.');
    }
}
