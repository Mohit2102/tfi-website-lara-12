<?php

namespace App\Services;

use App\Support\PlanPriceCatalog;
use InvalidArgumentException;

class PlanPricing
{
    public function __construct(private readonly PlanPriceRepository $prices = new PlanPriceRepository()) {}

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, usd: float, name: string, labels: array<string, string>}
     */
    public function priceFor(array $selection): array
    {
        $plan = $selection['plan'] ?? '';

        if (! is_string($plan)) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        if ($this->isFixedPlan($plan)) {
            return [
                'rupees' => $this->inr('fixed.'.$plan),
                'usd' => $this->usd('fixed.'.$plan),
                'name' => PlanPriceCatalog::definitions()['fixed.'.$plan]['label'],
                'labels' => [],
            ];
        }

        if ($plan === 'monthly-virtual' || $plan === '') {
            return $this->monthlyVirtualPrice($selection);
        }

        if ($plan === '12-weeks-virtual' || $plan === 'condition-12-weeks-virtual') {
            return $this->twelveWeekVirtualPrice($selection);
        }

        if ($plan === 'half-yearly-virtual') {
            return $this->halfYearlyVirtualPrice($selection);
        }

        throw new InvalidArgumentException('Invalid plan selection.');
    }

    public function fixedPriceFor(string $plan, string $currency = 'inr'): int|float|null
    {
        if (! $this->isFixedPlan($plan)) {
            return null;
        }

        return $currency === 'usd' ? $this->usd('fixed.'.$plan) : $this->inr('fixed.'.$plan);
    }

    /**
     * Price tables consumed by the public plan-builder scripts.
     *
     * @return array{inr: array<string, mixed>, usd: array<string, mixed>}
     */
    public function frontendPacks(): array
    {
        return [
            'inr' => $this->pack('inr'),
            'usd' => $this->pack('usd'),
        ];
    }

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, usd: float, name: string, labels: array<string, string>}
     */
    private function monthlyVirtualPrice(array $selection): array
    {
        $values = $this->validatedValues($selection, $this->monthlyLabels());
        $isCouple = $values['couple'] === 'yes';
        $baseKey = 'monthly.base.'.$values['trainer'].'.'.$values['session'];
        $trainingInr = $this->inr($baseKey);
        $trainingUsd = $this->usd($baseKey);

        if ($isCouple) {
            $trainingInr = (int) round($trainingInr * 1.5);
            $trainingUsd = round($trainingUsd * 1.5, 2);
        }

        $nutritionInr = $values['nutrition'] === 'yes' ? $this->inr('monthly.nutrition') * ($isCouple ? 2 : 1) : 0;
        $nutritionUsd = $values['nutrition'] === 'yes' ? $this->usd('monthly.nutrition') * ($isCouple ? 2 : 1) : 0;
        $extKey = 'monthly.extension.'.($isCouple ? 'couple' : 'solo').'.'.$values['extension'];
        $extensionInr = $values['extension'] === 'none' ? 0 : $this->inr($extKey);
        $extensionUsd = $values['extension'] === 'none' ? 0.0 : $this->usd($extKey);

        return [
            'rupees' => $trainingInr + $nutritionInr + $extensionInr,
            'usd' => round($trainingUsd + $nutritionUsd + $extensionUsd, 2),
            'name' => 'Monthly Virtual Training',
            'labels' => $values['labels'],
        ];
    }

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, usd: float, name: string, labels: array<string, string>}
     */
    private function twelveWeekVirtualPrice(array $selection): array
    {
        $values = $this->validatedValues($selection, $this->packageLabels());
        $session = $values['session'];
        $trainer = $values['trainer'];

        if (! in_array($session, ['3', '4', '5'], true) || ! isset($this->packageLabels()['trainer'][$trainer])) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        $trainingInr = $this->inr('12w.base.'.$session) + ($trainer === 'advanced' ? $this->inr('12w.trainer.advanced') : 0);
        $trainingUsd = $this->usd('12w.base.'.$session) + ($trainer === 'advanced' ? $this->usd('12w.trainer.advanced') : 0);

        if ($values['couple'] === 'yes') {
            $trainingInr += $this->inr('12w.couple');
            $trainingUsd += $this->usd('12w.couple');
        }

        $nutritionInr = $values['nutrition'] === 'yes' ? $this->inr('12w.nutrition') : 0;
        $nutritionUsd = $values['nutrition'] === 'yes' ? $this->usd('12w.nutrition') : 0;
        $extensionInr = $values['extension'] === '1' ? $this->inr('12w.extension') : 0;
        $extensionUsd = $values['extension'] === '1' ? $this->usd('12w.extension') : 0;
        $labels = $values['labels'];
        $labels['program'] = $this->programLabel($selection);

        return [
            'rupees' => $trainingInr + $nutritionInr + $extensionInr,
            'usd' => round($trainingUsd + $nutritionUsd + $extensionUsd, 2),
            'name' => $selection['plan'] === 'condition-12-weeks-virtual'
                ? $labels['program'].' 12 Weeks Virtual Training'
                : '12 Weeks Virtual Training',
            'labels' => $labels,
        ];
    }

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, usd: float, name: string, labels: array<string, string>}
     */
    private function halfYearlyVirtualPrice(array $selection): array
    {
        $values = $this->validatedValues($selection, $this->packageLabels());
        $session = $values['session'];
        $trainer = $values['trainer'];
        $monthlyKey = 'hy.trainer.'.$trainer.'.'.$session;

        if (! isset(PlanPriceCatalog::definitions()[$monthlyKey])) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        $trainingInr = (int) round($this->inr($monthlyKey) * 6 * 0.8);
        $trainingUsd = round($this->usd($monthlyKey) * 6 * 0.8, 2);

        if ($values['couple'] === 'yes') {
            $trainingInr = (int) round($trainingInr * 1.5);
            $trainingUsd = round($trainingUsd * 1.5, 2);
        }

        $nutritionInr = $values['nutrition'] === 'yes' ? $this->inr('hy.nutrition') : 0;
        $nutritionUsd = $values['nutrition'] === 'yes' ? $this->usd('hy.nutrition') : 0;
        $extensionInr = $values['extension'] === '1' ? $this->inr('hy.extension') : 0;
        $extensionUsd = $values['extension'] === '1' ? $this->usd('hy.extension') : 0;

        return [
            'rupees' => $trainingInr + $nutritionInr + $extensionInr,
            'usd' => round($trainingUsd + $nutritionUsd + $extensionUsd, 2),
            'name' => 'Half Yearly Virtual Training',
            'labels' => $values['labels'],
        ];
    }

    /**
     * @param  array<string, mixed>  $selection
     * @param  array<string, array<string, string>>  $allowed
     * @return array{session: string, couple: string, trainer: string, nutrition: string, extension: string, labels: array<string, string>}
     */
    private function validatedValues(array $selection, array $allowed): array
    {
        $values = [];
        $labels = [];

        foreach ($allowed as $name => $choices) {
            $value = $selection[$name] ?? '';

            if (! is_string($value) || ! isset($choices[$value])) {
                throw new InvalidArgumentException('Invalid plan selection.');
            }

            $values[$name] = $value;
            $labels[$name] = $choices[$value];
        }

        $values['labels'] = $labels;

        return $values;
    }

    /**
     * @param  array<string, mixed>  $selection
     */
    private function programLabel(array $selection): string
    {
        $program = $selection['program'] ?? 'Personal Training';

        if (! is_string($program) || strlen($program) > 100) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        return trim(strip_tags($program)) ?: 'Personal Training';
    }

    private function isFixedPlan(string $plan): bool
    {
        return isset(PlanPriceCatalog::definitions()['fixed.'.$plan]);
    }

    private function inr(string $key): int
    {
        $default = PlanPriceCatalog::definitions()[$key]['inr'] ?? 0;

        return $this->prices->inr($key, $default);
    }

    private function usd(string $key): float
    {
        return $this->prices->usd($key, 0);
    }

    /**
     * @return array<string, mixed>
     */
    private function pack(string $currency): array
    {
        $n = fn (string $key) => $currency === 'usd' ? $this->usd($key) : $this->inr($key);

        return [
            'monthly' => [
                'base' => [
                    'specialized' => ['12' => $n('monthly.base.specialized.12'), '16' => $n('monthly.base.specialized.16'), '20' => $n('monthly.base.specialized.20')],
                    'certified' => ['12' => $n('monthly.base.certified.12'), '16' => $n('monthly.base.certified.16'), '20' => $n('monthly.base.certified.20')],
                ],
                'nutrition' => $n('monthly.nutrition'),
                'extension' => [
                    'solo' => ['none' => 0, '10' => $n('monthly.extension.solo.10'), '30' => $n('monthly.extension.solo.30')],
                    'couple' => ['none' => 0, '10' => $n('monthly.extension.couple.10'), '30' => $n('monthly.extension.couple.30')],
                ],
            ],
            'twelveWeek' => [
                'base' => ['3' => $n('12w.base.3'), '4' => $n('12w.base.4'), '5' => $n('12w.base.5')],
                'defaultBase' => $n('12w.base.3'),
                'trainer' => ['standard' => 0, 'advanced' => $n('12w.trainer.advanced')],
                'couple' => $n('12w.couple'),
                'nutrition' => $n('12w.nutrition'),
                'ext' => $n('12w.extension'),
            ],
            'halfYearly' => [
                'monthly' => [
                    'standard' => ['3' => $n('hy.trainer.standard.3'), '4' => $n('hy.trainer.standard.4'), '5' => $n('hy.trainer.standard.5')],
                    'advanced' => ['3' => $n('hy.trainer.advanced.3'), '4' => $n('hy.trainer.advanced.4'), '5' => $n('hy.trainer.advanced.5')],
                ],
                'nutrition' => $n('hy.nutrition'),
                'ext' => $n('hy.extension'),
            ],
            'fixed' => [
                'monthly-nv' => $n('fixed.monthly-nv'),
                '12-weeks-nv' => $n('fixed.12-weeks-nv'),
                'half-yearly-nv' => $n('fixed.half-yearly-nv'),
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function monthlyLabels(): array
    {
        return [
            'session' => ['12' => '12 Sessions', '16' => '16 Sessions', '20' => '20 Sessions'],
            'couple' => ['no' => 'Solo', 'yes' => 'Couple'],
            'trainer' => [
                'certified' => 'Certified Personal Training',
                'specialized' => 'Specialized Personal Training',
            ],
            'nutrition' => ['no' => 'Without Nutrition', 'yes' => 'With Nutrition'],
            'extension' => ['none' => 'No Extension', '10' => '10-Day Extension', '30' => '30-Day Extension'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function packageLabels(): array
    {
        return [
            'session' => ['3' => '3 sessions per week', '4' => '4 sessions per week', '5' => '5 sessions per week'],
            'couple' => ['no' => 'Solo', 'yes' => 'Couple'],
            'trainer' => ['standard' => 'Specialized Personal Training', 'advanced' => 'Certified Personal Training'],
            'nutrition' => ['no' => 'Training only', 'yes' => 'With nutrition'],
            'extension' => ['none' => 'No extension', '1' => '+1 Month'],
        ];
    }
}
