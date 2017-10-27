<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQuestionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question_type_key')->unique();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        $questionTypes = array(
            array('id' => '1',	'question_type_key' => 'eDpKe77g5lwpqcUmnwSkpC93SLxiEG',	'name' => 'Text',		        'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '2',	'question_type_key' => 'RlCUSNeJiQ8YydYufaMxOoQ4tIGhbh',	'name' => 'Radio Buttons',	    'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '3',	'question_type_key' => 'zmuLJSV9Q8u9PZyGLroFADMVsvmo5f',	'name' => 'Check Box',	        'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '4',	'question_type_key' => '6KPLzvipmKo4JaBlmb7RKWypFV0GPs',	'name' => 'Text Area',		    'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '5',	'question_type_key' => 'AiTLznqDF6CqRXN8OLmqCzxVmK1ZT0',	'name' => 'Drop down',			'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '6',	'question_type_key' => '1xin2TLsCHyGavoWzLvB9imCJ8yBe9',	'name' => 'File',               'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null)
        );
        DB::table('question_types')->insert($questionTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('question_types');
    }
}
