<?php

namespace App\Traits;

use App\Helper;

trait CommonTrait
{
    public static function getOTP()
    {
        $otp = '123456';
        if (app()->isProduction()) {
            $otp = rand(100000, 999999);
        }

        return $otp;
        // return rand(100000, 999999);
    }
}
