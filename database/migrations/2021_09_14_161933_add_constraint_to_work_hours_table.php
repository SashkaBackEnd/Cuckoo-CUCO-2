<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstraintToWorkHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_hours', function (Blueprint $table) {
            DB::unprepared("ALTER TABLE work_hours ADD CONSTRAINT from_le_to CHECK(work_hours.`from` <= work_hours.`to`)");
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
            DB::unprepared("ALTER TABLE work_hours DROP CONSTRAINT from_le_to");
        });
    }
}
