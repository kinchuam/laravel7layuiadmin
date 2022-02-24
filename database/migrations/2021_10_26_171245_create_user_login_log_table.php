<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLoginLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username',120);
            $table->text('message');
            $table->string('platform',100)->default('');
            $table->string('browser',100)->default('');
            $table->ipAddress('ip');
            $table->string('ip_address', 300)->default('');
            $table->text('header');
            $table->timestamps();

            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_log');
    }
}
