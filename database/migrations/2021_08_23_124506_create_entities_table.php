<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->string('name');
            $table->string('customer_name');
            $table->string('customer_contacts');
            $table->string('phone')->unique();
            $table->longText('comment')->nullable();
            $table->string('address');
            $table->string('service_phone');
            $table->unsignedBigInteger('central_fast_id')->nullable()->unique();
            $table->timestamp('call_from')->default(Carbon::createFromTime(8));
            $table->timestamp('call_to')->default(Carbon::createFromTime(18));
            $table->unsignedInteger('quantity_calls')->default(1);
            $table->unsignedInteger('call_back_quantity')->default(1);
            $table->unsignedInteger('max_duration_work')->default(8);
            $table->foreign('central_fast_id', 'entities_central_fast_id_foreign_key')
                ->on('guarded_objects')
                ->references('id')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities');
    }
}
