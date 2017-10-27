<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_config', function (Blueprint $table) {
            $table->integer('form_id');
            $table->integer('form_configuration_id');
            $table->string('value');
            $table->timestamps();

            $keys = array('form_id','form_configuration_id');
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
        Schema::drop('form_config');
    }
}
