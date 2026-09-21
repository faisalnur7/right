<?php

namespace App\Services;

use Twilio\Rest\Client;

class OTPService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public function sendOTP($phone, $otp)
    {
        // Remove leading zero if present
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }
        
        $message = "Your OTP code is: $otp";
        

        return true;

        return $this->twilio->messages->create(
            '+880'.$phone,
            [
                'from' => env('TWILIO_PHONE'),
                'body' => $message,
            ]
        );
    }
}
