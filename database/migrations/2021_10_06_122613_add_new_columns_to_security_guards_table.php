<?php

use App\SecurityGuard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToSecurityGuardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_guards', function (Blueprint $table) {
            $table->enum('work_type', [SecurityGuard::WORK_TYPE_WATCH, SecurityGuard::WORK_TYPE_SHIFT]);
            $table->enum('status', [SecurityGuard::STATUS_COMMON, SecurityGuard::STATUS_SERVICE]);
            $table->date('license_to_date')->nullable();
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
            $table->dropColumn(['work_type', 'status', 'license_to_date']);
        });
    }
}
