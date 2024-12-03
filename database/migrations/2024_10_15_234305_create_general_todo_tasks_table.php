<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralTodoTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_todo_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
                $table->string('task_name')->nullable();
                $table->bigInteger('todosec_id')->unsigned();
                $table->foreign('todosec_id')->references('id')->on('general_todo_sections')->onDelete('cascade');
                $table->integer('taskorder')->default(0);
                $table->longText('description')->nullable();
                $table->dateTime('startdate')->nullable();
                $table->dateTime('enddate')->nullable();
                $table->integer('status')->comment('0 for inactive and 1 for active')->default(1);
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
        Schema::dropIfExists('general_todo_tasks');
    }
}
