<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstraintTableToCallFromAndCallToRowsForEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->time('call_to')->default(Carbon::createFromTime(23)->toTimeString())->change();
            $table->time('call_from')->default(Carbon::createFromTime(24)->toTimeString())->change();
            DB::unprepared("ALTER TABLE entities ADD CONSTRAINT call_from_le_then_call_to CHECK(entities.call_from <= entities.call_to)");
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
            DB::unprepared("ALTER TABLE entities DROP CONSTRAINT call_from_le_then_call_to");
        });
    }
}
