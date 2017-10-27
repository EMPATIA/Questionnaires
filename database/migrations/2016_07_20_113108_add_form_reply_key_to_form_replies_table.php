<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormReplyKeyToFormRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('form_replies', 'form_reply_key'))
            {
                $table->string('form_reply_key')->after('id');
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
        Schema::table('form_replies', function (Blueprint $table) {
            //
        });
    }
}
