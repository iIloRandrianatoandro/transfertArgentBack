<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionComplete extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    
    protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Argent transféré avec succès')
                    ->line('Votre argent a été transféré avec succès.')
                    ->line('Montant de la transaction : ' . $this->transaction->sommeTransaction )
                    ->line('Numéro de compte du destinataire : ' . $this->transaction->compteDestinataire)
                    //->action('Voir la transaction', url('/transactions/' . $this->transaction->id))
                    ->line('Merci de votre confiance!');
    }
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Votre argent de ' . $this->transaction->sommeTransaction . ' a été transféré avec succès au compte ' . $this->transaction->compteDestinataire . '.',
            'transaction_id' => $this->transaction->id,
            'completed_at' => $this->transaction->completed_at,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
