<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEventSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('key')->unique();
            $table->string('entity_id');
            $table->tinyInteger('type_id');            
            $table->string('title');
            $table->text('description');
            $table->string('local');
            $table->boolean('closed')->default(FALSE);
            $table->boolean('public');
            $table->string('created_by');
            $table->string('updated_by');
            $table->integer('es_period_id')->default(0);      
            $table->integer('es_question_id')->default(0);      
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
        Schema::drop('event_schedules');
    }
}
