<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Shop extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'tenants'; // Package uses 'tenants' table by default

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'owner_name',
            'phone',
            'address',
            'is_active',
            'subscription_starts_at',
            'subscription_ends_at',
        ];
    }
}