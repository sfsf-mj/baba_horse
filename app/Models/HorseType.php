<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorseType extends Model
{
    use HasFactory;
    
    protected $table = 'horse_types'; // اسم الجدول في قاعدة البيانات

    protected $fillable = ['class_types', 'price', 'ride_price']; // الحقول التي يمكن تعبئتها

    public $timestamps = true; // هذا يمنع Laravel من محاولة إدخال created_at و updated_at
}
