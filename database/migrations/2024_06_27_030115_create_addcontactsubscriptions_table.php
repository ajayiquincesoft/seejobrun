<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddcontactsubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addcontactsubscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('credits');
		    $table->longText('user_plan_id'); 
			$table->longText('stripe_plan_id');
			$table->longText('stripe_customer_id');
			$table->double('amount');
            $table->longText('transaction_id')->nullable();
            $table->dateTime('subscription_start_date')->nullable();
			$table->dateTime('subscription_end_date')->nullable();
            $table->longText('subscription_id')->nullable();
			$table->integer('plan_status')->comment('0 for unassign and 1 for assign')->default(0);
            $table->integer('subscription_status')->comment('0 for disabled 1 for enabled')->nullable();
			$table->bigInteger('contact_id')->nullable();
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
        Schema::dropIfExists('addcontactsubscriptions');
    }
}
