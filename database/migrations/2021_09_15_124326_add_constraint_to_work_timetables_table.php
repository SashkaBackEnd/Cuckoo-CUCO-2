<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstraintToWorkTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_timetables', function (Blueprint $table) {
            DB::unprepared("ALTER TABLE work_timetables ADD UNIQUE INDEX unique_work_day_of_guarded_object (day, guarded_objects_id)");
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
            DB::unprepared("ALTER TABLE work_timetables DROP INDEX unique_work_day_of_guarded_object");
        });
    }
}
