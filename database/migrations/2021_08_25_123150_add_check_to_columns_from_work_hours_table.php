<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCheckToColumnsFromWorkHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_hours', function (Blueprint $table) {
            DB::unprepared("ALTER TABLE work_hours ADD CONSTRAINT one_or_nothing CHECK(work_timetables_id is null OR work_timetables_date_id is null)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_hours', function (Blueprint $table) {
            DB::unprepared("ALTER TABLE work_hours drop constraint one_or_nothing");
        });
    }
}
