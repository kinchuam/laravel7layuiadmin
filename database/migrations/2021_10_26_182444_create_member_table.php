<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('openid',120)->default('');
            $table->string('unionid', 120)->default('');
            $table->string('nickname',130)->default('');
            $table->string('avatar',255)->default('');
            $table->tinyInteger('gender')->default(0);
            $table->string('realname',100)->default('');
            $table->string('mobile',30)->default('');
            $table->string('address', 300)->default('');
            $table->string('country',130)->default('');
            $table->string('province',60)->default('');
            $table->string('city',60)->default('');
            $table->uuid('uuid');
            $table->timestamps();

            $table->index('openid');
            $table->index('nickname');
            $table->index('uuid');
            $table->index(['realname', 'mobile']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member');
    }
}
