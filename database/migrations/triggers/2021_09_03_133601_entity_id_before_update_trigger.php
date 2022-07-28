<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EntityIdBeforeUpdateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
                CREATE TRIGGER check_entity_id_before_update
                    BEFORE UPDATE
                    ON entities
                    FOR EACH ROW
                    -- Проверка перед прикреплением центрального поста к объекту, где данный пост должен быть прикреплен к текущему объекту
                BEGIN
                    IF NEW.central_guarded_objects_id is not null AND NEW.id <> (SELECT entity_id FROM guarded_objects WHERE id = NEW.central_guarded_objects_id LIMIT 1) THEN
                        signal sqlstate '45000' set message_text = 'This guarded object is attached to another entity.';
                    END IF;
                END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS check_entity_id_before_update");
    }
}
