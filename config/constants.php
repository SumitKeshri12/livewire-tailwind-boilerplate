<?php

use Carbon\Carbon;

return [

    'export_pagination' => env('EXPORT_PAGINATION', 1000),
    'export_file_path' => 'custom_exports/',
    'export_txt_file_type' => 'txt',
    'export_csv_file_type' => 'csv',
    'export_xls_file_type' => 'xlsx',

    'site' => [
        'logo_url' => '/images/logo-letter-1.png',
    ],
    'brand' => [
        'status' => [
            'key' => [
                'active' => 'Y',
                'inactive' => 'N',
            ],
            'value' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
        ],
    ],
    'languages' => [
        'English' => 'en',
        'Hindi' => 'hi',
        'Arabic' => 'ar',
    ],

    'default_datetime_format' => 'd/m/Y H:i:s',
    'default_date_format' => 'd/m/Y',
    'default_time_format' => 'H:i:s',
    'validation_date_format' => 'Y-m-d',
    'validation_time_format' => 'H:i',

    'date_formats' => [
        'default' => 'jS F, Y  h:i a',
        'table_date' => 'd/m/Y',
        'table_datetime' => 'd/m/Y H:i:s',
    ],

    'import_csv_log' => [

        'status' => [
            'key' => [
                'success' => 'Y',
                'fail' => 'N',
                'pending' => 'P',
                'processing' => 'S',
                'convert_decrypted' => 'D',
            ],
            'value' => [
                'success' => 'Success',
                'fail' => 'Fail',
                'pending' => 'Pending',
                'processing' => 'Processing',
                'convert_decrypted' => 'Processing For Decrypted',
            ],
        ],

        'import_flag' => [
            'key' => [
                'success' => 'Y',
                'pending' => 'P',
            ],

            'value' => [
                'value' => [
                    'success' => 'Success',
                    'pending' => 'Pending',
                ],
            ],
        ],

        'import_email_recipients' => [
            'hello@yopmail.com',
        ],

        'models' => [
            'role' => 'roles',
            'user' => 'users',
            'brand' => 'brands',
        ],

        'subject' => [
            'role' => 'Role Import',
            'user' => 'User Import',
            'brand' => 'Brand Import',
        ],

        'folder_name' => [
            'new' => [
                'role' => 'import/new/role',
                'user' => 'import/new/users',
                'brand' => 'import/new/brand',
            ],
        ],
    ],
    'role' => [
        'status' => [
            'key' => [
                'yes' => 'Y',
                'no' => 'N',
            ],
            'value' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
        ],
    ],
    'import_type' => [
        'role' => 'roles',
        'user' => 'users',
        'brand' => 'brands',
    ],

    'validation_codes' => [
        'unauthorized' => 401,
        'forbidden' => 403,
        'unprocessable_entity' => 422,
        'unassigned' => 427,
        'rate_limit' => 429,
        'ok' => 200,
    ],

    'calender' => [
        'date' => Carbon::now()->toDateString(),
        'date_format' => Carbon::now()->format('Y-m-d'),
        'time' => Carbon::now()->toTimeString(),
        'date_time' => Carbon::now()->toDateTimeString(),
        'start_Of_month' => Carbon::now()->startOfMonth(),
        'last_year_date' => Carbon::now()->subYear()->format('Y-m-d'),
        'import_format' => Carbon::now()->format('d-M-Y'),
    ],

    'file' => [
        'name' => Carbon::now('Asia/Kolkata')->format('d_m_Y') . '_' . Carbon::now('Asia/Kolkata')->format('g_i_a'),
    ],

    'allowed_ip_addresses' => [
        'telescope' => env('TELESCOPE_ALLOWED_IP_ADDRESSES'),
        'pulse' => env('PULSE_ALLOWED_IP_ADDRESSES'),
    ],

    'token_expiry' => env('TOKEN_EXPIRY', (60 * 60 * 24)), // Default 24 hours

    'default_single_filesize' => 20,
    'default_file_extensions' => ['jpeg', 'jpg', 'png', 'webp'],

    'email_format' => [
        'type' => ['header' => '1', 'footer' => '2', 'signature' => '3'],
        'type_enum' => ['1', '2', '3'],

        'serialized' => [0 => 'Normal data', 1 => 'json format data'],
        'serialized_enum' => ['0', '1'],
    ],

    'email_template' => [
        'table' => [
            'table_name' => 'email_templates',
            'entity_name' => 'Email Template',
            'entity_name_plural' => 'Email Templates',
        ],
        'type' => [
            'user_login' => '1',
            'import_success' => '2',
            'import_fail' => '3',
            'change_password' => '4',
        ],

        'type_values' => [
            '1' => 'User Login',
            '2' => 'Import Success',
            '3' => 'Import Fail',
            '4' => 'Change Password',
        ],

        'status' => [
            'inactive' => 'N',
            'active' => 'Y',
        ],

        'status_values' => [
            'N' => 'Inactive',
            'Y' => 'Active',
        ],

        'status_message' => [
            'inactive' => 'Inactive',
            'active' => 'Active',
        ],

        'lagends' => [],

        'common_lagends' => [
            'admin_login_url' => '{{admin_login_url}}',
            'front_login_url' => '{{front_login_url}}',
            'reset_password_link' => '{{reset_password_link}}',
        ],
    ],

    'roles' => [
        'admin' => 1,
        'value' => [
            'admin' => 'Admin',
        ],
    ],

    'webPerPage' => '10',
    'webPerPageValues' => [10, 25, 50, 100, 250, 500, 750, 1000],

    'google_recaptcha_key' => ENV('GOOGLE_RECAPTCHA_KEY'),
    'google_recaptcha_secret' => env('GOOGLE_RECAPTCHA_SECRET'),

    'rate_limiting' => [
        'limit' => [
            'ip' => 1800, // 30 Minute Limit
            'otp' => 1800, // 30 Minute Limit
            'contact_number' => 1800, // 30 Minute Limit
            'forgot_password' => 60, // 1 Minute Limit
            'one_day' => 60 * 60 * 24,
            'one_hour' => 3600,
            'ip_attempt_limit' => 9,
            'email_attempt_limit' => 10,
        ],
        'message' => 'You have exceeded the allowed number of attempts, Please try again later.',
    ],

    'export_template_legend' => [
        '{{exportReport_downloadLink}}',
        '{{exportReport_modelName}}',
        '{{exportReport_dateTime}}',
        '{{exportReport_subject}}',
    ],

    'otp_counter_type' => [
        'login' => 'otpTimer',
    ],

    'user' => [
        'table' => [
            'table_name' => 'users',
            'entity_name' => 'User',
            'entity_name_plural' => 'Users',
            'create_route' => 'users.create',
            'add_method' => 'addUser',
            'export_method' => 'exportUsers',
            'default_values' => [
                'no_role' => 'No Role',
                'not_set' => 'Not Set',
                'invalid_time' => 'Invalid Time',
                'no_document' => 'No Document Uploaded',
                'no_languages' => 'No languages specified',
            ],
            'filters' => [
                'status' => [
                    ['value' => 'Y', 'label' => 'Active'],
                    ['value' => 'N', 'label' => 'Inactive'],
                ],
                'gender' => [
                    ['value' => 'M', 'label' => 'Male'],
                    ['value' => 'F', 'label' => 'Female'],
                ],
            ],
            'badge_classes' => [
                'no_role' => 'bg-gray-100 text-gray-600 border-gray-200',
                'active' => 'bg-green-100 text-green-800 border-green-200',
                'inactive' => 'bg-red-100 text-red-800 border-red-200',
                'roles' => [
                    'Admin' => 'bg-red-100 text-red-800 border-red-200',
                    'Manager' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'User' => 'bg-green-100 text-green-800 border-green-200',
                    'Editor' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'Viewer' => 'bg-gray-100 text-gray-800 border-gray-200',
                ],
            ],
        ],
        'gender' => [
            'key' => [
                'female' => 'F',
                'male' => 'M',
            ],
            'value' => [
                'female' => 'Female',
                'male' => 'Male',
            ],
            'labels' => [
                'F' => 'Female',
                'M' => 'Male',
            ],
        ],
        'status' => [
            'key' => [
                'active' => 'Y',
                'inactive' => 'N',
            ],
            'value' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
            'labels' => [
                'Y' => 'Active',
                'N' => 'Inactive',
            ],
        ],
        'hobbies' => [
            'reading' => 'Reading',
            'writing' => 'Writing',
            'music' => 'Music',
            'sports' => 'Sports',
            'gaming' => 'Gaming',
            'cooking' => 'Cooking',
            'travel' => 'Travel',
            'photography' => 'Photography',
        ],
        'skills' => [
            'php' => 'PHP',
            'javascript' => 'JavaScript',
            'python' => 'Python',
            'laravel' => 'Laravel',
            'react' => 'React',
            'vue' => 'Vue.js',
            'mysql' => 'MySQL',
            'postgresql' => 'PostgreSQL',
        ],
        'languages' => [
            'en' => 'English',
            'hi' => 'Hindi',
            'ar' => 'Arabic',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
        ],
        'file' => [
            'profile' => [
                'max_size' => 4096, // KB
                'extensions' => ['jpeg', 'png', 'jpg', 'gif', 'svg'],
                'directory' => 'user/',
            ],
            'document' => [
                'max_size' => 5120, // KB
                'max_files' => 5, // Maximum number of files
                'extensions' => ['pdf', 'doc', 'docx'],
                'directory' => 'user/',
            ],
        ],
        'events' => [
            'validation_failed' => 'validation-failed',
            'quill_reinit' => 'quill-reinit',
        ],
    ],

    'timezones' => [
        'UTC' => 'UTC',
        'America/New_York' => 'America/New_York (EST/EDT)',
        'America/Chicago' => 'America/Chicago (CST/CDT)',
        'America/Denver' => 'America/Denver (MST/MDT)',
        'America/Los_Angeles' => 'America/Los_Angeles (PST/PDT)',
        'America/Toronto' => 'America/Toronto (EST/EDT)',
        'America/Vancouver' => 'America/Vancouver (PST/PDT)',
        'Europe/London' => 'Europe/London (GMT/BST)',
        'Europe/Paris' => 'Europe/Paris (CET/CEST)',
        'Europe/Berlin' => 'Europe/Berlin (CET/CEST)',
        'Europe/Rome' => 'Europe/Rome (CET/CEST)',
        'Europe/Madrid' => 'Europe/Madrid (CET/CEST)',
        'Europe/Amsterdam' => 'Europe/Amsterdam (CET/CEST)',
        'Europe/Stockholm' => 'Europe/Stockholm (CET/CEST)',
        'Europe/Oslo' => 'Europe/Oslo (CET/CEST)',
        'Europe/Copenhagen' => 'Europe/Copenhagen (CET/CEST)',
        'Europe/Helsinki' => 'Europe/Helsinki (EET/EEST)',
        'Europe/Warsaw' => 'Europe/Warsaw (CET/CEST)',
        'Europe/Prague' => 'Europe/Prague (CET/CEST)',
        'Europe/Budapest' => 'Europe/Budapest (CET/CEST)',
        'Europe/Vienna' => 'Europe/Vienna (CET/CEST)',
        'Europe/Zurich' => 'Europe/Zurich (CET/CEST)',
        'Europe/Brussels' => 'Europe/Brussels (CET/CEST)',
        'Europe/Athens' => 'Europe/Athens (EET/EEST)',
        'Europe/Istanbul' => 'Europe/Istanbul (TRT)',
        'Europe/Moscow' => 'Europe/Moscow (MSK)',
        'Asia/Tokyo' => 'Asia/Tokyo (JST)',
        'Asia/Shanghai' => 'Asia/Shanghai (CST)',
        'Asia/Hong_Kong' => 'Asia/Hong_Kong (HKT)',
        'Asia/Singapore' => 'Asia/Singapore (SGT)',
        'Asia/Seoul' => 'Asia/Seoul (KST)',
        'Asia/Taipei' => 'Asia/Taipei (CST)',
        'Asia/Bangkok' => 'Asia/Bangkok (ICT)',
        'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
        'Asia/Manila' => 'Asia/Manila (PHT)',
        'Asia/Kolkata' => 'Asia/Kolkata (IST)',
        'Asia/Dubai' => 'Asia/Dubai (GST)',
        'Asia/Tehran' => 'Asia/Tehran (IRST)',
        'Asia/Karachi' => 'Asia/Karachi (PKT)',
        'Asia/Dhaka' => 'Asia/Dhaka (BST)',
        'Asia/Kathmandu' => 'Asia/Kathmandu (NPT)',
        'Asia/Colombo' => 'Asia/Colombo (SLST)',
        'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur (MYT)',
        'Asia/Ho_Chi_Minh' => 'Asia/Ho_Chi_Minh (ICT)',
        'Australia/Sydney' => 'Australia/Sydney (AEST/AEDT)',
        'Australia/Melbourne' => 'Australia/Melbourne (AEST/AEDT)',
        'Australia/Brisbane' => 'Australia/Brisbane (AEST)',
        'Australia/Perth' => 'Australia/Perth (AWST)',
        'Australia/Adelaide' => 'Australia/Adelaide (ACST/ACDT)',
        'Australia/Darwin' => 'Australia/Darwin (ACST)',
        'Pacific/Auckland' => 'Pacific/Auckland (NZST/NZDT)',
        'Pacific/Fiji' => 'Pacific/Fiji (FJT)',
        'Pacific/Honolulu' => 'Pacific/Honolulu (HST)',
        'Pacific/Guam' => 'Pacific/Guam (ChST)',
        'Africa/Cairo' => 'Africa/Cairo (EET)',
        'Africa/Johannesburg' => 'Africa/Johannesburg (SAST)',
        'Africa/Lagos' => 'Africa/Lagos (WAT)',
        'Africa/Casablanca' => 'Africa/Casablanca (WEST)',
        'Africa/Nairobi' => 'Africa/Nairobi (EAT)',
        'America/Sao_Paulo' => 'America/Sao_Paulo (BRT)',
        'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires (ART)',
        'America/Mexico_City' => 'America/Mexico_City (CST/CDT)',
        'America/Lima' => 'America/Lima (PET)',
        'America/Bogota' => 'America/Bogota (COT)',
        'America/Caracas' => 'America/Caracas (VET)',
        'America/Santiago' => 'America/Santiago (CLT)',
    ],

    'sms_template' => [
        'status' => [
            'key' => [
                'active' => 'Y',
                'inactive' => 'N',
            ],
            'value' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
        ],
    ],

    'template_type' => [
        'otp_verification' => 'OTP',
    ],

    'template_id' => [
        'otp_verification' => '1107173936080544333',
    ],

    'template_name' => [
        'OTP' => 'OTP Verification',
    ],

];
