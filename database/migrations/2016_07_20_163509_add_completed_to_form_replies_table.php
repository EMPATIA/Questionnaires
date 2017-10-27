<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompletedToFormRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('form_replies', 'completed'))
            {
                $table->boolean('completed')->after('form_id')->nullable();
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
