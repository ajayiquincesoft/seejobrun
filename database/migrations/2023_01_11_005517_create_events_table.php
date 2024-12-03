<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->string('title')->nullable();
			$table->longText('description')->nullable();
			$table->dateTime('startdate')->nullable();
            $table->dateTime('enddate')->nullable();
			$table->integer('notification_alert')->comment('Notification Alert before minutes')->nullable();
			$table->integer('status')->comment('0 for inactive and 1 for active')->default(0);
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
        Schema::dropIfExists('events');
    }
}
