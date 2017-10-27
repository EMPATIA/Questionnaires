<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTitleTypeToTextInQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('question_translations', 'question'))
            {
                $table->text('question')->change();
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
        Schema::table('question_translations', function (Blueprint $table) {
            if (Schema::hasColumn('question_translations', 'question'))
            {
            }
        });
    }
}
