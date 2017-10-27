<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_type_id');
            $table->string('name');
            $table->string('language_code');
            $table->timestamps();
            $table->softDeletes();
        });

        $questionTypeTranslations = array(
            array('id' => '1',	'question_type_id' => '1',	'name' => 'Text',		            'language_code' => 'en',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '2',	'question_type_id' => '1',	'name' => 'Texto',	                'language_code' => 'pt',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '3',	'question_type_id' => '2',	'name' => 'Radio Buttons',		    'language_code' => 'en',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '4',	'question_type_id' => '2',	'name' => 'Radio Buttons',	        'language_code' => 'pt',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '5',	'question_type_id' => '3',	'name' => 'Check Box',	            'language_code' => 'en',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '6',	'question_type_id' => '3',	'name' => 'Check Box',              'language_code' => 'pt',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '7',	'question_type_id' => '4',	'name' => 'Text Area',			    'language_code' => 'en',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '8',	'question_type_id' => '4',	'name' => 'Área de Texto',          'language_code' => 'pt',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '9',	'question_type_id' => '5',	'name' => 'Dropdown',			    'language_code' => 'en',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '10',	'question_type_id' => '5',	'name' => 'Dropdown',               'language_code' => 'pt',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '11',	'question_type_id' => '6',	'name' => 'File',			        'language_code' => 'en',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '12',	'question_type_id' => '6',	'name' => 'Ficheiro',               'language_code' => 'pt',  'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
        );
        DB::table('question_type_translations')->insert($questionTypeTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_type_translations');
    }
}
