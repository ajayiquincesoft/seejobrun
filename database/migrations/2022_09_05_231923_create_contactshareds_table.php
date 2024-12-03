<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsharedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contactshareds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->Integer('jobnotepad')->comment('0 for view own and 1 for view all');
            $table->Integer('punchlist')->comment('0 for view own and 1 for view all');
            $table->Integer('stage')->comment('0 for blank and 1 for view all');
            $table->Integer('contact')->comment('0 for blank and 1 for view all');
            $table->Integer('document')->comment('0 for blank and 1 for view all');
			$table->Integer('calendar')->comment('0 for blank and 1 for view all');
            $table->Integer('pictures')->comment('0 for blank and 1 for view all');
			$table->Integer('general')->comment('0 for hide and 1 for view all');
            $table->Integer('todo')->comment('0 for hide and 1 for view all');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contactshareds');
    }
}
