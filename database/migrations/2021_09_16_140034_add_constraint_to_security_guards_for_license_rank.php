<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstraintToSecurityGuardsForLicenseRank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_guards', function (Blueprint $table) {
            DB::unprepared('ALTER TABLE security_guards ADD CONSTRAINT if_license_then_license_rank_not_null CHECK ( license_rank between 1 AND 9 )');
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
            DB::unprepared('ALTER TABLE security_guards DROP CONSTRAINT if_license_then_license_rank_not_null');
        });
    }
}
