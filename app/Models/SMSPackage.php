<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSPackage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sms_count', 'price'];
}
