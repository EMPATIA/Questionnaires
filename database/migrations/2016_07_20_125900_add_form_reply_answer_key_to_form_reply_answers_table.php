<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormReplyAnswerKeyToFormReplyAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_reply_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('form_reply_answers', 'form_reply_answer_key'))
            {
                $table->string('form_reply_answer_key')->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_reply_answers', function (Blueprint $table) {
            //
        });
    }
}
