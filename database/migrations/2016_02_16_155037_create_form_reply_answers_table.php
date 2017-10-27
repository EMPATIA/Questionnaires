<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateFormReplyAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_reply_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_reply_id');
            $table->integer('question_id');
            $table->integer('question_option_id');
            $table->text('answer');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('form_reply_answers');
    }
}
