<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('hour');
            $table->unsignedInteger('day');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->unsignedInteger('type');
            $table->decimal('amount',20,2)->default(0);
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->useCurrent();

            $table->index('user_id');
            $table->index('hour');
            $table->index('day');
            $table->index('month');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commission_statistics');
    }
}
