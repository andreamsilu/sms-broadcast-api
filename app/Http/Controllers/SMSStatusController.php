<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhoneNumber;

class SMSStatusController extends Controller
{
    public function checkStatus()
    {
        $statuses = PhoneNumber::where('user_id', auth()->id())
            ->select('phone_number', 'status', 'response', 'updated_at')
            ->get();

        return response()->json($statuses);
    }
}

