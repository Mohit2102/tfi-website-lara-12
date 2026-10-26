<?php

namespace Database\Seeders;

use App\Models\PlanPrice;
use App\Support\PlanPriceCatalog;
use Illuminate\Database\Seeder;

class PlanPriceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PlanPriceCatalog::definitions() as $key => $definition) {
            PlanPrice::firstOrCreate(
                ['key' => $key],
                [
                    'group' => $definition['group'],
                    'label' => $definition['label'],
                    'amount_inr' => $definition['inr'],
                    'amount_usd' => 0,
                    'sort_order' => $definition['sort'],
                ]
            );
        }
    }
}
