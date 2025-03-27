<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendSmsJob;
use App\Models\Broadcast;
use App\Models\Contact;

class BroadcastController extends Controller
{
    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:160',
        ]);

        $contacts = Contact::where('user_id', auth()->id())->get();

        $broadcast = Broadcast::create([
            'user_id' => auth()->id(),
            'sender_name' => auth()->user()->name,
            'message' => $request->message,
            'total_recipients' => $contacts->count(),
            'status' => 'queued',
        ]);

        foreach ($contacts as $contact) {
            SendSmsJob::dispatch($contact->phone_number, $request->message, $broadcast);
        }

        return response()->json(['message' => 'Broadcast queued successfully']);
    }
}
