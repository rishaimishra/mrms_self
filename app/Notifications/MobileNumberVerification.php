<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class MobileNumberVerification extends Notification
{
    use Queueable;
    public $code;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code)
    {

        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }



    public function toTwilio($notifiable)
    {

        return (new TwilioSmsMessage())
            ->content("Your " . env('APP_NAME') . " verification code is:{$this->code}");
    }

    public function canReceiveAlphanumericSender()
    {
        return true;
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
