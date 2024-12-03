<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClocktimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clocktimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->String('clockin_latitude')->nullable();
            $table->String('clockin_longitude')->nullable();
            $table->String('clockout_latitude')->nullable();
            $table->String('clockout_longitude')->nullable();
            $table->date('tdate')->nullable();
            $table->Time('clockin')->nullable();
            $table->Time('clockout')->nullable();
			$table->integer('clockstatus')->comment('1 for clockin and 0 for clockout')->default(1);
            $table->integer('injoyed')->comment('1 for Yes and 0 for No')->default(0);
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('clocktimes');
    }
}
