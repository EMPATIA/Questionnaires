<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_options', function (Blueprint $table) {
            if (!Schema::hasColumn('question_options', 'position'))
            {
                $table->integer('position')->after('file_code');
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
        Schema::table('question_options', function (Blueprint $table) {
            if (Schema::hasColumn('question_options', 'position'))
            {
                $table->dropColumn('position');
            }
        });
    }
}
