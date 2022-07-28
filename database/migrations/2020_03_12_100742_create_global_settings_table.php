<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name')->unique();
            $table->string('value');
        });

        DB::table('global_settings')->insert(
            [
                'name' => 'dialing_status',
                'value' => '0'
            ]
        );
        DB::table('global_settings')->insert(
            [
                'name' => 'calls_per_day',
                'value' => '5'
            ]
        );
        DB::table('global_settings')->insert(
            [
                'name' => 'shift_change_time',
                'value' => '5'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_settings');
    }
}
