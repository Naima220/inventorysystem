<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
       $table->id();

    $table->unsignedBigInteger('customer_id');
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

    // Order details
    $table->integer('order_status')->default(0); // 0 = Pending, 1 = Delivered
    $table->string('customer_name');
    $table->string('customer_phone');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}