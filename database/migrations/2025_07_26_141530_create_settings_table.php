<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable(); // Store time as string (e.g., "08:00:00")
            $table->timestamps();
        });

        // Seed some default values
        DB::table('settings')->insert([
            ['key' => 'jam_masuk_start', 'value' => '08:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jam_masuk_end', 'value' => '09:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jam_keluar_min', 'value' => '17:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
