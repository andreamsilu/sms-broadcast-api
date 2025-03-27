<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Broadcast;
use App\Models\PhoneNumber;

class DashboardController extends Controller
{
    // Get SMS Broadcast History with Status Summary
    public function getBroadcasts()
    {
        $broadcasts = Broadcast::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->withCount([
                'phoneNumbers as total_recipients',
                'phoneNumbers as sent_count' => function ($query) {
                    $query->where('status', 'sent');
                },
                'phoneNumbers as failed_count' => function ($query) {
                    $query->where('status', 'failed');
                }
            ])
            ->get();

        return response()->json($broadcasts);
    }

    // Get Detailed Statistics for a Specific Broadcast
    public function getBroadcastDetails($id)
    {
        $broadcast = Broadcast::where('user_id', auth()->id())->with('phoneNumbers')->find($id);

        if (!$broadcast) {
            return response()->json(['message' => 'Broadcast not found'], 404);
        }

        return response()->json([
            'broadcast' => $broadcast,
            'total_recipients' => $broadcast->phoneNumbers->count(),
            'sent_count' => $broadcast->phoneNumbers->where('status', 'sent')->count(),
            'failed_count' => $broadcast->phoneNumbers->where('status', 'failed')->count(),
        ]);
    }

    // Get Dashboard Summary Statistics
    public function getStats()
    {
        $totalBroadcasts = Broadcast::where('user_id', auth()->id())->count();
        $totalRecipients = PhoneNumber::whereHas('broadcast', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();
        $sentMessages = PhoneNumber::whereHas('broadcast', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('status', 'sent')->count();
        $pendingMessages = PhoneNumber::whereHas('broadcast', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('status', 'queued')->count();
        $failedMessages = PhoneNumber::whereHas('broadcast', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('status', 'failed')->count();

        return response()->json([
            'total_broadcasts' => $totalBroadcasts,
            'total_recipients' => $totalRecipients,
            'sent_messages' => $sentMessages,
            'pending_messages' => $pendingMessages,
            'failed_messages' => $failedMessages,
        ]);
    }
}
