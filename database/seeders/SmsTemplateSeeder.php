<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Cache;
use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmsTemplate::truncate();
        Cache::forget('getSmsTemplates');
        $data =
            [
                [
                    'type' => config('constants.template_type.otp_verification'),
                    'template_name' => config('constants.template_name.OTP'),
                    'message' => 'Dear User, your OTP for logging in Admin is {{users_otp}}. Do not share this with anyone. Valid for 90 seconds. Powered by Admin',
                    'template_id' => config('constants.template_id.otp_verification'),
                    'status' => config('constants.sms_template.status.key.active'),
                    'created_at' => config('constants.calender.date_time'),
                    'updated_at' => config('constants.calender.date_time'),
                ],
            ];

        SmsTemplate::insert([
            ...$data,
        ]);
    }
}
