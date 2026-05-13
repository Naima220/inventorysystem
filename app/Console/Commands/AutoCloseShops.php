<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use Carbon\Carbon;

class AutoCloseShops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shops:auto-close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deactivate shops with expired subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        
        $expiredShops = Shop::where('is_active', 1)
                            ->whereNotNull('subscription_ends_at')
                            ->where('subscription_ends_at', '<', $now)
                            ->get();

        $count = $expiredShops->count();

        foreach ($expiredShops as $shop) {
            $shop->is_active = 0;
            $shop->save();
            $this->info("Closed shop: {$shop->name} (Expired on {$shop->subscription_ends_at})");
        }

        if ($count > 0) {
            $this->info("Successfully closed {$count} expired shops.");
        } else {
            $this->info("No expired shops found.");
        }

        return 0;
    }
}
