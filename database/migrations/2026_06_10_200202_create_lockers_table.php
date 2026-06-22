<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('lockers', function (Blueprint $table) {
        $table->id();
        $table->string('locker_number', 10)->unique();
        $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
        $table->timestamp('last_updated')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lockers');
    }
};
