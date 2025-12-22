<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Service Provider : https://xyz.com/
    |--------------------------------------------------------------------------
    |
    | Tripart account credentials will be required to access the API documentation
    |
    */

    'api_key'   => env('SMS_API_KEY'),
    'sender_id' => env('SMS_SENDER_ID'),
    'base_url'  => env('SMS_URL'),

];
