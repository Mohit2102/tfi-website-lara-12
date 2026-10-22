<?php

namespace Tests\Unit;

use App\Services\PlanPricing;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PlanPricingTest extends TestCase
{
    public function test_it_prices_a_solo_certified_plan(): void
    {
        $quote = (new PlanPricing)->priceFor([
            'session' => '12',
            'couple' => 'no',
            'trainer' => 'certified',
            'nutrition' => 'no',
            'extension' => 'none',
        ]);

        $this->assertSame(9999, $quote['rupees']);
    }

    public function test_it_rejects_invalid_selections(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PlanPricing)->priceFor([
            'session' => '99',
            'couple' => 'no',
            'trainer' => 'certified',
            'nutrition' => 'no',
            'extension' => 'none',
        ]);
    }

    public function test_it_prices_twelve_week_virtual_options_on_the_server(): void
    {
        $quote = (new PlanPricing)->priceFor([
            'plan' => '12-weeks-virtual',
            'session' => '3',
            'couple' => 'yes',
            'trainer' => 'advanced',
            'nutrition' => 'yes',
            'extension' => '1',
        ]);

        $this->assertSame(15999, $quote['rupees']);
        $this->assertSame('12 Weeks Virtual Training', $quote['name']);
    }

    public function test_it_prices_half_yearly_virtual_options_on_the_server(): void
    {
        $quote = (new PlanPricing)->priceFor([
            'plan' => 'half-yearly-virtual',
            'session' => '3',
            'couple' => 'no',
            'trainer' => 'standard',
            'nutrition' => 'yes',
            'extension' => '1',
        ]);

        $this->assertSame(78500, $quote['rupees']);
    }

    public function test_fixed_non_virtual_plan_prices_cannot_be_overridden_by_the_browser(): void
    {
        $pricing = new PlanPricing;
        $quote = $pricing->priceFor([
            'plan' => '12-weeks-nv',
            'rupees' => 1,
        ]);

        $this->assertSame(7499, $quote['rupees']);
        $this->assertSame(27999, $pricing->fixedPriceFor('half-yearly-nv'));
    }
}
