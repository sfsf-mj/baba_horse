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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('horse_type');
            $table->string('ride_level');
            $table->string('name');
            $table->integer('age');
            $table->string('gender');
            $table->string('Whatsapp_number');
            $table->string('phone');
            $table->date('date');
            $table->time('time');
            $table->string('offer')->nullable();
            $table->string('booking_type');
            $table->integer('group_size')->default(0);
            $table->decimal('total_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
