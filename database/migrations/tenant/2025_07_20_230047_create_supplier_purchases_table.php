<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('supplier_purchases', function (Blueprint $table) {
            $table->id();
            $table->decimal('t_cost', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('sup_cost', 10, 2); // total after discount
            $table->decimal('paid', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('supplier_name', 100);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('supplier_purchases');
    }
};