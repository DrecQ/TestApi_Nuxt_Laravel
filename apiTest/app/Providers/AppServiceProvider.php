<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {

            $parts = parse_url($url);
            $verifyEmailUrl = 'http://localhost:3000/auth/verify-email?id='.
                $notifiable->getKey() . '&hash=' . sha1($notifiable->getEmailForVerification()) . '&' . $parts['query'];
             // Personnalisation de l'email de vérification
            return (new MailMessage)
                ->subject('Veuillez vérifier votre adresse e-mail')
                ->greeting('Bonjour ' . $notifiable->firstname . ',')  // Tu peux accéder au nom de l'utilisateur
                ->line('Merci de vous être inscrit sur notre site.')
                ->line('Avant de continuer, veuillez vérifier votre adresse e-mail en cliquant sur le bouton ci-dessous.')
                ->action('Vérifier mon e-mail', $verifyEmailUrl)
                ->line('Si vous n\'avez pas créé de compte, vous pouvez ignorer cet e-mail.')
                ->salutation('Cordialement,')  // Personnalisation de la fin du mail
                ->salutation('L\'équipe de [Nom de ton app]');
        });
    }
}
