<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuedCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queued_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('call_date');
            $table->integer('guarded_object_id');
            $table->string('call_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queued_calls');
    }
}
