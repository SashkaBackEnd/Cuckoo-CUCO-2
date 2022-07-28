<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWorkHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('to');
            $table->time('from');
            $table->unsignedBigInteger('work_timetables_id')->nullable();
            $table->foreign('work_timetables_id', 'work_hours_work_timetables_id_foreign')
                ->on('work_timetables')
                ->onDelete('cascade')
                ->onUpdate('restrict')
                ->references('id');
            $table->unsignedBigInteger('work_timetables_date_id')->nullable();
            $table->foreign('work_timetables_date_id', 'work_hours_work_timetables_date_id_foreign')
                ->on('work_timetable_dates')
                ->onDelete('cascade')
                ->onUpdate('restrict')
                ->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_hours');
    }
}
