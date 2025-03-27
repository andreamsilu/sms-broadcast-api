<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SMSService
{
    public function sendSMS($phone, $message)
    {
        $response = Http::post('https://your-sms-gateway.com/api/send', [
            'api_key' => env('SMS_API_KEY'),
            'sender_id' => env('SMS_SENDER_ID'),
            'phone' => $phone,
            'message' => $message,
        ]);

        return $response->json();
    }
}
