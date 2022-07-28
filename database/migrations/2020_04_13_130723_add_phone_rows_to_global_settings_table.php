<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneRowsToGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('global_settings')->insert(
            [
                'name' => 'phone_2',
                'value' => '+79000000000'
            ]
        );
        DB::table('global_settings')->insert(
            [
                'name' => 'phone_3',
                'value' => '+79000000000'
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
        DB::table('global_settings')->where('name','phone_2')->delete();
        DB::table('global_settings')->where('name','phone_3')->delete();

    }
}
