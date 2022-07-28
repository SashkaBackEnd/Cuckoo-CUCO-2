<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSalaryColumnInWorkTimetables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_timetables', function (Blueprint $table) {
            DB::statement('ALTER TABLE work_timetables CHANGE  salary salary FLOAT NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_timetables', function (Blueprint $table) {
            DB::statement('ALTER TABLE work_timetables CHANGE salary salary NOT NULL');
        });
    }
}
