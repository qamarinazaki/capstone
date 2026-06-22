<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->string('esp_ip')->nullable()->after('locker_number');
        });
    }

    public function down()
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->dropColumn('esp_ip');
        });
    }
};