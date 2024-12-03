<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('contact_user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->longText('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('shared_contact')->nullable();
            $table->longText('contact_notes')->nullable();
            $table->string('business_name')->nullable();
            $table->string('license_no')->nullable();
            $table->string('trade')->nullable();
            $table->string('social_security_no')->nullable();
            $table->integer('gps_tracker')->comment('0 for disable and 1 for enable')->default(0);
            $table->integer('status')->comment('0 for inactive and 1 for active')->default(1);
            $table->integer('type')->comment('1 for client, 2 for sub_contractor and 3 for employee')->default(0);
			$table->longText('subscription_id')->nullable();
			$table->longText('subscription_status')->nullable();
			$table->dateTime('subscription_start')->nullable();
			$table->dateTime('subscription_end')->nullable();
			$table->longText('subscription_end_reason')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
