<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkTimetableDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_timetable_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('day');
            $table->integer('salary');
            $table->unsignedBigInteger('guarded_objects_id');
            $table->foreign('guarded_objects_id', 'work_timetable_dates_guarded_objects_id_foreign')
                ->on('guarded_objects')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('work_timetable_dates');
    }
}
