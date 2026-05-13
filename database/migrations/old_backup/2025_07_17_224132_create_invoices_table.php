<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('customer_id');
        $table->string('customer_name');
        $table->string('customer_phone');
        $table->decimal('debt', 10, 2)->default(0);
        $table->timestamps();

        // Foreign key constraint (optional but recommended)
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    });
}

   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
