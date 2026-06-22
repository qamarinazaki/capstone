<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parcel_assignments', function (Blueprint $table) {
            $table->id();
            
            // We create the structural columns explicitly
            $table->unsignedBigInteger('parcel_id');
            $table->unsignedBigInteger('locker_id');
            
            $table->string('pickup_code', 10)->unique();
            $table->string('customer_phone')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamps();
            
            // We remove the strict physical MySQL constraint lines to prevent the crash
        });
    }

    public function down()
    {
        Schema::dropIfExists('parcel_assignments');
    }
};