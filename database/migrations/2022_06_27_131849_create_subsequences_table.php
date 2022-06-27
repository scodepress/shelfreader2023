<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubsequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subsequences', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('user_id');
            $table->string('barcode', 20);
            $table->integer('shelf_position');
            $table->integer('correct_position');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subsequences');
    }
}
