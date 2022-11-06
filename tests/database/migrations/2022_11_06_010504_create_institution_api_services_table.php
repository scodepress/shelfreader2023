<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionApiServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institution_api_services', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('institution_id')->nullable();
            $table->integer('library_id');
            $table->integer('api_service_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->integer('loaded')->nullable();
            $table->integer('sort_scheme_id');
            $table->string('sort_scheme_name', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institution_api_services');
    }
}
