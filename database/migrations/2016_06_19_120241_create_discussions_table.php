<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('channel_id')->unsigned();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->integer('comments_count');
            $table->integer('best_comment_id')->unsigned();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_sticky')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users');

            $table->foreign('channel_id')
                  ->references('id')->on('channels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('discussions');
    }
}
