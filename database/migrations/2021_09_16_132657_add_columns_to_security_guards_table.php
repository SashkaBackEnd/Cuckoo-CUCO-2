<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSecurityGuardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_guards', function (Blueprint $table) {
            $table->integer('license_rank')->nullable();
            $table->string('knew_about_us')->nullable();
            $table->string('left_things')->nullable();
            $table->boolean('driving_license')->default(0);
            $table->string('car')->nullable();
            $table->string('medical_book')->nullable();
            $table->string('gun')->nullable();
            $table->string('debts')->nullable();
            $table->string('work_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('security_guards', function (Blueprint $table) {
            $table->dropColumn([
                'license_rank',
                'knew_about_us',
                'left_things',
                'driving_license',
                'car',
                'medical_book',
                'gun',
                'debts',
                'work_type'
            ]);
        });
    }
}
