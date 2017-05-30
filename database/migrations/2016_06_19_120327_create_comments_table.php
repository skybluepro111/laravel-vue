<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('discussion_id')->unsigned();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->boolean('approved')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users');

            $table->foreign('discussion_id')
                  ->references('id')->on('discussions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
