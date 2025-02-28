<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'horse_type', 'ride_level', 'name', 'age', 'gender', 'Whatsapp_number', 
        'phone', 'date', 'time', 'offer', 'booking_type', 'group_size', 'total_price', 'status'
    ];
}
