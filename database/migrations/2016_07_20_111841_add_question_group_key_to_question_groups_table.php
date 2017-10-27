<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionGroupKeyToQuestionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('question_groups', 'question_group_key'))
            {
                $table->string('question_group_key')->after('id');
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
        Schema::table('question_groups', function (Blueprint $table) {
            //
        });
    }
}
