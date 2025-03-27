<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserSMSBalance;
use App\Models\PaymentTransaction;

class AzamPesaController extends Controller
{
    // Generate Access Token
    private function getAccessToken()
    {
        $response = Http::post(env('AZAMPESA_BASE_URL') . '/auth/token', [
            'client_id' => env('AZAMPESA_CLIENT_ID'),
            'client_secret' => env('AZAMPESA_CLIENT_SECRET'),
            'grant_type' => env('AZAMPESA_GRANT_TYPE'),
        ]);

        return $response->json()['access_token'] ?? null;
    }

    // Initiate Payment Request
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:1000',
        ]);

        $token = $this->getAccessToken();
        if (!$token) {
            return response()->json(['message' => 'Failed to get access token'], 500);
        }

        // Unique Transaction ID
        $transactionId = 'TXN' . time();

        $response = Http::withToken($token)->post(env('AZAMPESA_BASE_URL') . '/payments/request', [
            'shortcode' => env('AZAMPESA_SHORTCODE'),
            'amount' => $request->amount,
            'msisdn' => $request->phone_number,
            'reference' => $transactionId,
            'callback_url' => env('AZAMPESA_CALLBACK_URL'),
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Payment request failed'], 500);
        }

        // Save transaction record
        PaymentTransaction::create([
            'user_id' => auth()->id(),
            'transaction_id' => $transactionId,
            'amount' => $request->amount,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Payment initiated, waiting for confirmation']);
    }

    // Handle AzamPesa Callback
    public function paymentCallback(Request $request)
    {
        $data = $request->all();
        $transaction = PaymentTransaction::where('transaction_id', $data['reference'])->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($data['status'] == 'SUCCESS') {
            $transaction->update(['status' => 'completed']);

            // Update user SMS balance
            $userBalance = UserSMSBalance::firstOrCreate(['user_id' => $transaction->user_id]);
            $smsCredits = $transaction->amount * 10; // Example: 10 SMS per 1 TZS
            $userBalance->sms_balance += $smsCredits;
            $userBalance->save();

            return response()->json(['message' => 'Payment successful, SMS credits added!']);
        } else {
            $transaction->update(['status' => 'failed']);
            return response()->json(['message' => 'Payment failed'], 400);
        }
    }
}

