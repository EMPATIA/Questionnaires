<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEsPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('es_periods', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('event_schedule_id');
            $table->date('start_date');
            $table->string('start_time');            
            $table->date('end_date');       
            $table->string('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('es_participant_es_period', function (Blueprint $table) {
            $table->integer('es_period_id');
            $table->integer('es_participant_id');                 
            $table->string('attendance'); 
            $table->string('created_by');              
            $table->timestamps();
            $keys = array('es_period_id','es_participant_id');
            $table->primary($keys);               
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('es_periods');
        Schema::drop('es_participant_es_period');
    }
}
