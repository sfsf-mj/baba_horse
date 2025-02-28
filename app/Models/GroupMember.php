<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'member_horse_type', 'member_level', 'member_name', 'member_age', 'member_gender', 'status'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

}
