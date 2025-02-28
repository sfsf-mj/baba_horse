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
        Schema::table('horse_types', function (Blueprint $table) {
            $table->integer('price')->change();
            $table->integer('ride_price')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horse_types', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('ride_price', 10, 2)->change();
        });
    }
};
