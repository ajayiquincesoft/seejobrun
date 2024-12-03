<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('profile_pic')->nullable();
            $table->integer('user_type')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->integer('is_varified')->default(0);
            $table->integer('otp')->default(0);
			$table->integer('register_otp')->default(0);
			$table->string('devicetype')->nullable();
			$table->string('timezone')->nullable();
			$table->longText('stripe_customer_id')->nullable();
			$table->bigInteger('credit_contact')->default(2);
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
        Schema::dropIfExists('users');
    }
}
