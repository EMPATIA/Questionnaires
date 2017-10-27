<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEsParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('es_participants', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('key')->unique();
            $table->integer('event_schedule_id');            
            $table->string('name'); 
            $table->string('user_key');             
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->softDeletes();          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('es_participants');
    }
}
