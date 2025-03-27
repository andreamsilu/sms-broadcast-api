<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Models\UserSMSBalance;

class PaymentDashboardController extends Controller
{
    // Get Payment History
    public function getPaymentHistory(Request $request)
    {
        $payments = PaymentTransaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }

    // Get Specific Payment Details
    public function getPaymentDetails($id)
    {
        $payment = PaymentTransaction::where('user_id', auth()->id())->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json($payment);
    }

    // Get SMS Credit Balance
    public function getSMSBalance()
    {
        $userBalance = UserSMSBalance::where('user_id', auth()->id())->first();

        return response()->json([
            'sms_balance' => $userBalance ? $userBalance->sms_balance : 0,
        ]);
    }

    // Get Dashboard Payment Statistics
    public function getPaymentStats()
    {
        $totalPayments = PaymentTransaction::where('user_id', auth()->id())->count();
        $totalAmountSpent = PaymentTransaction::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->sum('amount');
        $pendingPayments = PaymentTransaction::where('user_id', auth()->id())->where('status', 'pending')->count();
        $failedPayments = PaymentTransaction::where('user_id', auth()->id())->where('status', 'failed')->count();

        return response()->json([
            'total_payments' => $totalPayments,
            'total_amount_spent' => $totalAmountSpent,
            'pending_payments' => $pendingPayments,
            'failed_payments' => $failedPayments,
        ]);
    }
}

