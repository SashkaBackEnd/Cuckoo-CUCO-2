<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromSecurityGuardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_guards', function (Blueprint $table) {
            $table->dropColumn(['nationality', 'sex', 'schedule', 'active', 'work_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('security_guards', function (Blueprint $table) {
            $table->string('nationality');
            $table->integer('sex');
            $table->string('schedule');
            $table->integer('active')->default(1);
        });
    }
}
