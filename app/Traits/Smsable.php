<?php

namespace App\Traits;

use App\Helper;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Notifications\SmsNotification;

trait Smsable
{

    /**
     * Send OTP SMS using MTalkz service
     *
     * @param User $user The user to send OTP to
     */
    public static function sendUserOtpBySMS($user, $userOtp, $extraLegendValues = null)
    {
        $smsTemplate = self::getSmsTemplate(config('constants.template_type.otp_verification'));
        if (is_null($smsTemplate)) {
            Helper::logError(static::class, __FUNCTION__, __('messages.record_not_found'), [
                'userOtp'   => $userOtp,
            ]);
            return;
        }

        $modelArray = [];
        $extraLegendValues['{{users_otp}}'] = $userOtp;
        // Generate dynamic message content and send notification
        $message = User::getDynamicContent($smsTemplate->message, $modelArray, $extraLegendValues);

        if ($user instanceof User) {
            $user->notify(new SmsNotification($message));
        } else {
            Helper::logError(static::class, __FUNCTION__, 'User is not an instance of User', ['user' => $user]);
        }
    }

    /**
     * Retrieve an SMS template by organization and template ID
     *
     * @param int $organizationId The organization identifier
     * @param int $smsTemplateId The SMS template identifier
     * @return SmsTemplate|null Returns the SMS template if found, null otherwise
     */
    public static function getSmsTemplate($type)
    {
        return SmsTemplate::where('type', $type)
            ->where('status', config('constants.sms_template.status.key.active'))
            ->first();
    }

}
