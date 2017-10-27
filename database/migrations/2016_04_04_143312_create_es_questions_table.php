<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEsQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('es_questions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('event_schedule_id');
            $table->string('question');            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('es_participant_es_question', function (Blueprint $table) {
            $table->integer('es_question_id');
            $table->integer('es_participant_id');                 
            $table->string('attendance'); 
            $table->string('created_by');              
            $table->timestamps();
            $keys = array('es_question_id','es_participant_id');
            $table->primary($keys,'pk_question_participant');               
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('es_questions');
        Schema::drop('es_participant_es_question');
    }
}
