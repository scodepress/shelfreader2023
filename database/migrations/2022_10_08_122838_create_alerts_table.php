<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto incrementing primary key
            $table->integer('user_id');
            $table->bigInteger('library_id');
            $table->integer('sort_scheme_id');
            $table->string('barcode', 30)->nullable();
            $table->string('call_number')->nullable();
            $table->text('title')->nullable();
            $table->string('alert');
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
        //
    }
};
