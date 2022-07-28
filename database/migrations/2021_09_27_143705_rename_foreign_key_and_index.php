<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameForeignKeyAndIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->foreign('central_guarded_objects_id', 'entities_central_guarded_objects_id_foreign_key')
                ->on('guarded_objects')
                ->references('id')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('central_guarded_objects_id')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities', function (Blueprint $table) {
            //
        });
    }
}
