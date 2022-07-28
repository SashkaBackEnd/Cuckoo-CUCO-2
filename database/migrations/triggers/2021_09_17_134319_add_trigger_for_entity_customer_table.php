<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTriggerForEntityCustomerTable extends Migration
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
            CREATE TRIGGER check_quantity_customers_before_insert
                BEFORE INSERT
                ON entity_customers
                FOR EACH ROW
                -- Проверка кол-ва имеющихся заказчиков у объекта
            BEGIN
                IF get_quantity_customers(NEW.entity_id) >= 5 THEN
                    signal sqlstate '45000' set message_text = 'Entity must have no more than 5 customers ';
                END IF;
            END;");
            DB::unprepared("
            CREATE TRIGGER check_quantity_customers_before_update
                BEFORE UPDATE
                ON entity_customers
                FOR EACH ROW
                -- Проверка кол-ва имеющихся заказчиков у объекта
            BEGIN
                IF get_quantity_customers(NEW.entity_id) >= 5 THEN
                    signal sqlstate '45000' set message_text = 'Entity must have no more than 5 customers ';
                END IF;
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
            DB::unprepared("DROP TRIGGER IF EXISTS check_quantity_customers_before_insert");
            DB::unprepared("DROP TRIGGER IF EXISTS check_quantity_customers_before_update");
        });
    }
}
