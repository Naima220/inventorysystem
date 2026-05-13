<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionStartToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('shops', function (Blueprint $table) {
        $table->dateTime('subscription_starts_at')->nullable()->after('subscription_expiry');
    });
}

public function down()
{
    Schema::table('shops', function (Blueprint $table) {
        $table->dropColumn('subscription_starts_at');
    });
}
    
}