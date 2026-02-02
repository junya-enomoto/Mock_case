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
            $table->string('user_image')->nullable();
            $table->string('user_name');
            $table->string('email')->unique(); 
            $table->string('password');
            $table->string('postal_code')->nullable();
            $table->string('street_address')->nullable();
            $table->string('building_name')->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
    Schema::dropIfExists('orders');
    Schema::dropIfExists('comments');
    Schema::dropIfExists('likes');
    Schema::dropIfExists('item_categories'); 
    Schema::dropIfExists('items');

    Schema::dropIfExists('categories');
 
    Schema::dropIfExists('personal_access_tokens');
    Schema::dropIfExists('failed_jobs');
    Schema::dropIfExists('password_resets');
    Schema::dropIfExists('users');
    }
}
