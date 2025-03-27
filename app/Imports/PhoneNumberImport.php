<?php
namespace App\Imports;

use App\Models\PhoneNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PhoneNumberImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new PhoneNumber([
            'user_id' => auth()->id(),
            'phone_number' => $row['phone_number'],
        ]);
    }
}
