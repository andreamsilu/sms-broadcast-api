<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PhoneNumberImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PhoneNumber;
use App\Jobs\SendBulkSMS;

class FileUploadController extends Controller
{
    public function uploadContacts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx|max:2048',
        ]);

        Excel::import(new PhoneNumberImport, $request->file('file'));

        // Queue SMS sending after contacts are imported
        SendBulkSMS::dispatch(auth()->id())->onQueue('sms_queue');

        return response()->json(['message' => 'Contacts uploaded, SMS messages are being processed']);
    }
}
