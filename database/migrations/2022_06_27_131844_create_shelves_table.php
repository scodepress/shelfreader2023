<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('scan_order');
            $table->bigInteger('user_id');
            $table->string('callnumber', 250);
            $table->string('barcode', 250);
            $table->text('title');
            $table->bigInteger('shelf_position');
            $table->bigInteger('correct_position');
            $table->string('status', 45)->nullable();
            $table->string('effective_shelving_order', 250)->nullable();
            $table->string('effective_location_id', 250);
            $table->string('effective_location_name', 250)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shelves');
    }
}
