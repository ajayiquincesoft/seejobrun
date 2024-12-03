<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->longText('title');
			$table->longText('body');
            $table->longText('type')->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
			$table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('status')->comment('0 for read , 1 for unread')->default(1);
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
        Schema::dropIfExists('notifications');
    }
}
