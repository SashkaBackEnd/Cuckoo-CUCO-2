<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPatronymicHosColumnForGuardedObjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guarded_objects', function (Blueprint $table) {
            $table->dropColumn('patronymic_hos');
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
            $table->string('patronymic_hos');
        });
    }
}
