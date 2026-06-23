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
        Schema::table('ninjavan_data', function (Blueprint $table) {
            
            // 1. Kolum Berat
            if (!Schema::hasColumn('ninjavan_data', 'Original_Weight')) {
                $table->decimal('Original_Weight', 8, 2)->nullable();
            }

            // 2. Kolum Status (Punca ralat terbaharu)
            if (!Schema::hasColumn('ninjavan_data', 'Order_Granular_Status')) {
                $table->string('Order_Granular_Status')->nullable();
            }
            
            // 3. Kolum Negeri / State (Sediaan untuk dashboard)
            if (!Schema::hasColumn('ninjavan_data', 'L1_Name')) {
                $table->string('L1_Name')->nullable();
            }
            
            // 4. Kolum Saiz Parcel (Sediaan untuk dashboard)
            if (!Schema::hasColumn('ninjavan_data', 'Parcel_Size_ID')) {
                $table->string('Parcel_Size_ID')->nullable();
            }
            
            // 5. Kolum Jantina (Sediaan untuk dashboard)
            if (!Schema::hasColumn('ninjavan_data', 'Gender')) {
                $table->string('Gender')->nullable();
            }
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ninjavan_data', function (Blueprint $table) {
            $columns = ['Original_Weight', 'Order_Granular_Status', 'L1_Name', 'Parcel_Size_ID', 'Gender'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('ninjavan_data', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};