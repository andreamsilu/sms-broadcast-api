<?php

namespace App\Jobs;

use Twilio\Rest\Client;
use App\Models\Broadcast;
use App\Models\UserSMSBalance;
use Illuminate\Support\Facades\Log;

class SendSmsJob extends Job
{
    protected $recipient, $message, $broadcast, $user;

    public function __construct($recipient, $message, $broadcast, $user)
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->broadcast = $broadcast;
        $this->user = $user;
    }

    public function handle()
    {
        // Check SMS balance for the user
        $userBalance = UserSMSBalance::where('user_id', $this->user->id)->first();

        if (!$userBalance || $userBalance->sms_balance < 1) {
            Log::error("Insufficient SMS balance for user {$this->user->id}");
            return;
        }

        // Deduct SMS count (assuming sending one SMS per recipient)
        $userBalance->sms_balance -= 1;
        $userBalance->save();

        // Send SMS via Twilio API
        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create(
                $this->recipient,
                [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $this->message
                ]
            );

            // Update broadcast status in database
            $this->broadcast->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            Log::info("SMS sent successfully to {$this->recipient}!");

        } catch (\Exception $e) {
            Log::error("Failed to send SMS to {$this->recipient}: " . $e->getMessage());
        }
    }
}
