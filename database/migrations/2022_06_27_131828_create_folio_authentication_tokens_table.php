<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolioAuthenticationTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folio_authentication_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('institution_id');
            $table->string('institution_name', 50);
            $table->string('auth_key', 300)->unique('auth_key');
            $table->string('tenant', 50);
            $table->string('folio_username', 50);
            $table->string('folio_password', 50);
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
        Schema::dropIfExists('folio_authentication_tokens');
    }
}
