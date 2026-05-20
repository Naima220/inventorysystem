<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use Stancl\Tenancy\Database\Models\Domain;

class FixShopDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-shop-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing tenant domains to match the current APP_URL configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting domain cleanup...');

        $appUrl = config('app.url');
        $centralDomain = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
        $centralDomain = explode(':', $centralDomain)[0];

        $this->info("Current Environment Central Domain: {$centralDomain}");

        $domains = Domain::all();

        if ($domains->isEmpty()) {
            $this->warn('No domains found in the database.');
            return 0;
        }

        $headers = ['Tenant ID', 'Old Domain', 'New Domain', 'Status'];
        $rows = [];

        foreach ($domains as $domainRecord) {
            $tenantId = $domainRecord->tenant_id;
            $oldDomain = $domainRecord->domain;
            $newDomain = strtolower($tenantId) . '.' . $centralDomain;

            if ($oldDomain !== $newDomain) {
                $domainRecord->domain = $newDomain;
                $domainRecord->save();
                $rows[] = [$tenantId, $oldDomain, $newDomain, '✅ Updated'];
            } else {
                $rows[] = [$tenantId, $oldDomain, $newDomain, '➖ No Change'];
            }
        }

        $this->table($headers, $rows);
        $this->info('Domain cleanup completed successfully!');

        return 0;
    }
}
