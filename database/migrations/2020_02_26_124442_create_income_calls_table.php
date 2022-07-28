<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('call_date');
            $table->string('phone_number');
            $table->bigInteger('call_id');
            $table->string('call_status');
            $table->string('phone_input')->nullable();
            $table->string('process_status')->nullable();
            $table->integer('guarded_object_id')->nullable();
            $table->integer('security_guard_id')->nullable();
            $table->string('action_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_calls');
    }
}
