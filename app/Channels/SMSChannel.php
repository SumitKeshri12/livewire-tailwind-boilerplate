<?php

namespace App\Channels;

use App\Helper;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SMSChannel
{

    /**
     * Send an SMS notification via MTalkZ service.
     *
     * @param mixed $notifiable The entity receiving the notification
     * @param \Illuminate\Notifications\Notification $notification The notification instance
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {

        try {
            // Get the message content from the notification
            /** @var \App\Notifications\SMSNotification $notification */
            $message = $notification->toSMS($notifiable);
            // Get the recipient's phone number
            $number = $notifiable->routeNotificationForSMS();

            // Prepare the API request payload
            // Initialize base response array
            $request = [
                'apikey'   => config('sms-service.api_key'),
                'senderid' => config('sms-service.sender_id'),
                'format'   => 'json',
                'number'   => "+91" . $number,
                'message'  => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($message->message))))
            ];
            // Log the SMS request details
            Helper::logSingleInfo(static::class, __FUNCTION__, 'SMSChannel sent SMS', [
                'Request payload' => $request
            ]);

            // Please send the SMS request using your Tripart API URL.
            $response = Http::post(config('sms-service.base_url'), $request);
            Log::info('Tripart API Response:', [
                'response' => $response->json()
            ]);

        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);
        }
    }
}
