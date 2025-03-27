<?php
namespace App\Jobs;

use App\Models\PhoneNumber;
use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(SMSService $smsService)
{
    $phoneNumbers = PhoneNumber::where('user_id', $this->userId)->where('status', 'pending')->get();

    foreach ($phoneNumbers as $phone) {
        $response = $smsService->sendSMS($phone->phone_number, "Your bulk SMS message here");

        if ($response['status'] === 'success') {
            $phone->update(['status' => 'sent', 'response' => json_encode($response)]);
        } else {
            $phone->update(['status' => 'failed', 'response' => json_encode($response)]);
        }
    }
}

}
