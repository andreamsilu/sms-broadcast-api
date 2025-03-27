<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SMSPackage;
use App\Models\UserSMSBalance;

class SMSBillingController extends Controller
{
    // Get Available SMS Packages
    public function getPackages()
    {
        return response()->json(SMSPackage::all());
    }

    // Purchase SMS Package
    public function purchasePackage(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:sms_packages,id',
        ]);

        $package = SMSPackage::find($request->package_id);
        $user = auth()->user();

        // Deduct payment logic (integrate with payment gateway here)
        // Example: Assume payment is successful

        // Update User's SMS Balance
        $balance = UserSMSBalance::firstOrCreate(['user_id' => $user->id]);
        $balance->sms_balance += $package->sms_count;
        $balance->save();

        return response()->json(['message' => 'Package purchased successfully!', 'new_balance' => $balance->sms_balance]);
    }

    // Get User SMS Balance
    public function getUserBalance()
    {
        $balance = UserSMSBalance::where('user_id', auth()->id())->first();
        return response()->json(['sms_balance' => $balance ? $balance->sms_balance : 0]);
    }
}

