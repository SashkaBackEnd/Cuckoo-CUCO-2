<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurityGuardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('security_guards', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->primary('id');
            $table->integer('pin');
            $table->timestamps();
            $table->softDeletes();
            $table->string('surname');
            $table->string('name');
            $table->string('patronymic');
            $table->date('birth_date');
            $table->string('nationality');
            $table->integer('sex');
            $table->string('phone');
            $table->string('schedule');
            $table->integer('license');
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('security_guards');
    }
}
