<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionTypeKeyToQuestionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_types', function (Blueprint $table) {
            if (!Schema::hasColumn('question_types', 'question_type_key'))
            {
                $table->string('question_type_key')->after('id');
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
        Schema::table('question_types', function (Blueprint $table) {
            //
        });
    }
}
