<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->default(0);
            $table->string('filename',150)->default('');
            $table->string('path',255)->default('');
            $table->string('suffix',20)->default('');
            $table->string('type',100)->default('');
            $table->string('storage',50)->default('');
            $table->integer('size')->default(0);
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();

            $table->index('group_id');
            $table->index('filename');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachment');
    }
}
