<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPhoneHosColumnForGuardedObjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guarded_objects', function (Blueprint $table) {
            $table->dropColumn('phone_hos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guarded_objects', function (Blueprint $table) {
            $table->string('phone_hos');
        });
    }
}
