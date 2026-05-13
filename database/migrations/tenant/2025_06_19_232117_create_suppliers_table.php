<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.              
     *
     * @return void
     */
    public function up()
    {
       Schema::create('suppliers', function (Blueprint $table) {
        $table->bigIncrements('id'); 
        $table->string('supplier_name');
        $table->string('email')->unique();
        $table->enum('gender', ['Male', 'Female']);
        $table->string('address');
        $table->string('phone');
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
        Schema::dropIfExists('suppliers');
    }
}

