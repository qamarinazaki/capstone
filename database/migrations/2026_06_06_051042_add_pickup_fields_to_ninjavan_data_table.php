<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ninjavan_data', function (Blueprint $table) {
            $table->string('pickup_code')->unique()->nullable()->after('id');
            $table->timestamp('picked_up_at')->nullable()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('ninjavan_data', function (Blueprint $table) {
            $table->dropColumn(['pickup_code', 'picked_up_at']);
        });
    }
};