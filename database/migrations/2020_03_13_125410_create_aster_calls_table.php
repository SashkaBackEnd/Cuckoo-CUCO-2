<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsterCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aster_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('guarded_object_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('direction')->nullable();
            $table->string('call_result')->nullable();
            $table->string('phone_input')->nullable();
            $table->dateTime('call_date')->nullable();
            $table->integer('queued_call_id')->nullable();
            $table->string('process_status')->nullable();
            $table->integer('talk_duration')->nullable();
            $table->integer('total_duration')->nullable();
            $table->string('last_app')->nullable();
            $table->string('d_context')->nullable();
            $table->integer('ama_flags')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aster_calls');
    }
}
