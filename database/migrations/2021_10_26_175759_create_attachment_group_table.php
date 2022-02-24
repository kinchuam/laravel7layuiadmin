<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',120);
            $table->tinyInteger('sort')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachment_group');
    }
}
