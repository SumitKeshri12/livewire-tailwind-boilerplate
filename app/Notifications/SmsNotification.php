<?php

namespace App\Notifications;


use App\Channels\SMSChannel;
use App\Helper;
use App\Services\SMSMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return [SMSChannel::class];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toSMS($notifiable): SMSMessage
    {
        try {
            Log::info('SmsNotification Message: ' . $this->message);
            return (new SMSMessage())
                ->message($this->message);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            Helper::logCatchError($th, static::class, __FUNCTION__, ['message' => $this->message]);
        }
    }
}
