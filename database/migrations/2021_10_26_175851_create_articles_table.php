<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sort')->default(0);
            $table->bigInteger('category_id')->default(0);
            $table->string('title',150)->default('');
            $table->string('thumb',255)->default('');
            $table->string('desc',500)->default('');
            $table->string('url',500)->default('');
            $table->mediumText('content');
            $table->bigInteger('view_count')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->index('category_id');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
