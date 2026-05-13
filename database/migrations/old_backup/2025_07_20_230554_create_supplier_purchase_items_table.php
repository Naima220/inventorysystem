<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('supplier_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('cost_price', 10, 2);
            $table->integer('qty');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            $table->foreign('supplier_purchase_id')->references('id')->on('supplier_purchases')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('supplier_purchase_items');
    }
};