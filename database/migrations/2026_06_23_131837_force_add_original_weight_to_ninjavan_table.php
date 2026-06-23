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
        // Guna 'ninjavan_data' supaya sepadan dengan database di Railway
        Schema::table('ninjavan_data', function (Blueprint $table) {
            
            // Kita semak dahulu, jika kolum 'Original_Weight' belum ada, baru kita tambah
            if (!Schema::hasColumn('ninjavan_data', 'Original_Weight')) {
                $table->decimal('Original_Weight', 8, 2)->nullable();
            }
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ninjavan_data', function (Blueprint $table) {
            if (Schema::hasColumn('ninjavan_data', 'Original_Weight')) {
                $table->dropColumn('Original_Weight');
            }
        });
    }
};