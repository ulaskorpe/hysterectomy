<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    protected Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
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
        ->subject('Yeni rezervasyon talebi')
        ->line('Lokasyon: '.$this->appointment->json_data['location'] ?? '')
        ->line('Ad Soyad: '.$this->appointment->name)
        ->line('Email: '.$this->appointment->phone)
        ->line('Telefon: '.$this->appointment->phone)
        ->line('Medikal Bilgiler: '.$this->appointment->message)
        ->action('Görüntüle', route('appointments.show',$this->appointment->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
