<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path',150);
            $table->string('method', 10);
            $table->text('input');
            $table->ipAddress('ip');
            $table->string('ip_address', 300)->default('');
            $table->string('platform',100)->default('');
            $table->string('browser',100)->default('');
            $table->text('header');
            $table->timestamps();

            $table->index('path');
            $table->index('ip');
            $table->index('platform');
            $table->index('browser');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_log');
    }
}
