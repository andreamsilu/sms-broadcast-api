<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhoneNumber;

class SMSDeliveryController extends Controller
{
    public function updateDeliveryStatus(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'status' => 'required|string',
            'message_id' => 'nullable|string',
        ]);

        $phoneNumber = PhoneNumber::where('phone_number', $request->phone)->first();

        if ($phoneNumber) {
            $phoneNumber->update([
                'status' => $request->status,
                'response' => json_encode($request->all()),
            ]);
        }

        return response()->json(['message' => 'Delivery status updated']);
    }
}
