<?php
 namespace App\Jobs;

 use Twilio\Rest\Client;
 use App\Models\Broadcast;

 class SendSmsJob extends Job
 {
     protected $recipient, $message, $broadcast;

     public function __construct($recipient, $message, $broadcast)
     {
         $this->recipient = $recipient;
         $this->message = $message;
         $this->broadcast = $broadcast;
     }

     public function handle()
     {
         $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

         $twilio->messages->create(
             $this->recipient,
             [
                 'from' => env('TWILIO_PHONE_NUMBER'),
                 'body' => $this->message
             ]
         );

         // Update status in database
         $this->broadcast->update(['status' => 'sent', 'sent_at' => now()]);
     }
 }
