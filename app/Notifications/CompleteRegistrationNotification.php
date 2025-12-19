<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CompleteRegistrationNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'complete-registration.show',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Confirmar email e completar registo')
            ->line('Para ativar a tua conta, confirma o email e completa o registo (telemóvel + NIF).')
            ->action('Completar registo', $url)
            ->line('Se não criaste conta, ignora este email.');
    }
}
