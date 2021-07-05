<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvsSecretsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tvs_secrets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('media_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('playlist_name');
            $table->string('playlist_path');
            $table->tinyInteger('converted')->default('1');
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tvs_secrets');
    }
}
