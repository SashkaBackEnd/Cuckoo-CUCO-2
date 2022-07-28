<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFunctionForEntityCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_customers', function (Blueprint $table) {
            DB::unprepared("
            CREATE function get_quantity_customers(entity INTEGER)
            RETURNS INTEGER
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                return (select COUNT(id) FROM entity_customers WHERE entity_id = entity);
            END;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entity_customers', function (Blueprint $table) {
            DB::unprepared('DROP FUNCTION IF EXISTS get_quantity_customers');
        });
    }
}
