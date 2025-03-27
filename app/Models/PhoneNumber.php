<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'broadcast_id', 'phone_number', 'status', 'response'];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }
}

