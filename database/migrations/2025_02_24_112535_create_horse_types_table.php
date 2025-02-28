<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horse_types', function (Blueprint $table) {
            $table->id();
            $table->string('class_types'); // نوع الحصان
            $table->decimal('price', 10, 2); // سعر الحصان
            $table->decimal('ride_price', 10, 2); // سعر الركوب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horse_types');
    }
};
