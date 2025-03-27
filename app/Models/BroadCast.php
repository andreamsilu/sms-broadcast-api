<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message', 'status', 'total_recipients', 'sent_count', 'failed_count'];

    public function phoneNumbers()
    {
        return $this->hasMany(PhoneNumber::class);
    }
}
