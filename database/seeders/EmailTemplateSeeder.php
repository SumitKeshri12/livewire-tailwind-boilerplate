<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'type' => config('constants.email_template.type.user_login'),
                'label' => 'User Login OTP',
                'subject' => 'Your login Verification Code',
                'body' => '<!DOCTYPE html><html><head></head><body><p>Hello,</p><p>Welcome to Admin Portal.</p><p> Your Login OTP is: {{user_otp}}</p><p>&nbsp;</p></body></html>',
                'status' => 'Y',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'type' => config('constants.email_template.type.import_success'),
                'label' => 'Import Success',
                'subject' => '{{subject}}',
                'body' => '<!DOCTYPE html><html><head></head><body><p>#{{row_count}} The {{model_type}} ({{file_name}} file)  which you have imported has been successfully imported. Kindly click on below link for import history.</p><p>&nbsp;</p></body></html>',
                'status' => 'Y',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'type' => config('constants.email_template.type.import_fail'),
                'label' => 'Import Failed',
                'subject' => '{{subject}}',
                'body' => '<!DOCTYPE html><html><head></head><body><p>The {{model_type}} ({{file_name}} file) which you have imported has failed due to some reasons. Kindly click on below link for import history.</p><p>&nbsp;</p></body></html>',
                'status' => 'Y',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'type' => config('constants.email_template.type.change_password'),
                'label' => 'Change Password',
                'subject' => '{{subject}}',
                'body' => '<!DOCTYPE html><html><head></head><body><p>Your password change successfully.</p><p>&nbsp;</p></body></html>',
                'status' => 'Y',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        EmailTemplate::insert($templates);
    }
}
