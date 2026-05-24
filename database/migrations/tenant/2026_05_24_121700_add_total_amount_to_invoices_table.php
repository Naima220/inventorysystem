<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalAmountToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('invoices', 'total_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('total_amount', 10, 2)->default(0.00)->after('debt');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('invoices', 'total_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('total_amount');
            });
        }
    }
}
