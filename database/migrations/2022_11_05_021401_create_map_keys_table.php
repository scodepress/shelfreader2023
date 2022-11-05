<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('barcode', 20);
            $table->string('callnumber', 100);
            $table->string('class_letter')->nullable();
            $table->string('class_number', 4)->nullable();
            $table->string('class_decimal', 10)->nullable();
            $table->string('topline_dotted_cutter_letter', 5)->nullable();
            $table->string('topline_dotted_cutter_decimal', 10)->nullable();
            $table->string('neighborhood_cutter', 10)->nullable();
            $table->string('first_undotted_cutter_letter', 5)->nullable();
            $table->string('first_undotted_cutter_decimal', 10)->nullable();
            $table->string('scale_name', 10)->nullable();
            $table->integer('scale_number')->nullable();
            $table->integer('publication_date')->nullable();
            $table->string('nextline_dotted_cutter_letter', 3)->nullable();
            $table->integer('nextline_dotted_cutter_decimal')->nullable();
            $table->string('specification', 250)->nullable();
            $table->string('year_of_reproduction', 10)->nullable();
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
        Schema::dropIfExists('map_keys');
    }
}
