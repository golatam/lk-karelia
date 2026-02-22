<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject("Восстановление пароля на сайте " . url("/"))
            ->line("Вы получили это письмо, потому что мы получили запрос на сброс пароля для Вашей учетной записи.")
            ->action("Сбросить пароль", route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]))
            ->line("Если Вы не запрашивали сброс пароля, никаких дополнительных действий не требуется.")
            ->markdown('emails.notifications.password.reset.email', ['token' => $this->token, 'email' => $notifiable->email]);
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
