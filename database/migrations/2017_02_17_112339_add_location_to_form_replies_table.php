<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationToFormRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('form_replies', 'location'))
            {
                $table->string('location')->nullable()->after('step');
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
        Schema::table('form_replies', function (Blueprint $table) {
            //
        });
    }
}
