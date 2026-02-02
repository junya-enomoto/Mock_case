<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('item_id')->constrained()->onDelete('cascade'); 

            $table->string('payment_method'); 
            $table->string('postal_code');    
            $table->string('street_address'); 
            $table->string('building_name')->nullable(); 
            
            $table->integer('price'); 
            $table->string('status')->default('pending');

            $table->timestamps();
            $table->unique(['user_id', 'item_id']); 
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
    }
}
