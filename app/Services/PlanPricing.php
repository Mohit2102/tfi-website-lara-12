<?php

namespace App\Services;

use InvalidArgumentException;

class PlanPricing
{
    private const MONTHLY_BASE = [
        'specialized' => ['12' => 15000, '16' => 20000, '20' => 25000],
        'certified' => ['12' => 9999, '16' => 13332, '20' => 16665],
    ];

    private const VIRTUAL_12_WEEK_BASE = ['3' => 7999, '4' => 9999, '5' => 11999];

    private const HALF_YEARLY_TRAINER = [
        'standard' => ['3' => 15000, '4' => 20000, '5' => 25000],
        'advanced' => ['3' => 9999, '4' => 13332, '5' => 16665],
    ];

    private const MONTHLY_EXTENSION = [
        'solo' => ['none' => 0, '10' => 999, '30' => 2499],
        'couple' => ['none' => 0, '10' => 1499, '30' => 3499],
    ];

    private const FIXED_PLANS = [
        'monthly-nv' => ['name' => 'Monthly Non-Virtual Training', 'rupees' => 5999],
        '12-weeks-nv' => ['name' => '12 Weeks Non-Virtual Transformation', 'rupees' => 7499],
        'half-yearly-nv' => ['name' => 'Half Yearly Non-Virtual Transformation', 'rupees' => 27999],
    ];

    private const MONTHLY_LABELS = [
        'session' => ['12' => '12 Sessions', '16' => '16 Sessions', '20' => '20 Sessions'],
        'couple' => ['no' => 'Solo', 'yes' => 'Couple'],
        'trainer' => [
            'certified' => 'Certified Personal Training',
            'specialized' => 'Specialized Personal Training',
        ],
        'nutrition' => ['no' => 'Without Nutrition', 'yes' => 'With Nutrition'],
        'extension' => ['none' => 'No Extension', '10' => '10-Day Extension', '30' => '30-Day Extension'],
    ];

    private const PACKAGE_LABELS = [
        'session' => ['3' => '3 sessions per week', '4' => '4 sessions per week', '5' => '5 sessions per week'],
        'couple' => ['no' => 'Solo', 'yes' => 'Couple'],
        'trainer' => ['standard' => 'Specialized Personal Training', 'advanced' => 'Certified Personal Training'],
        'nutrition' => ['no' => 'Training only', 'yes' => 'With nutrition'],
        'extension' => ['none' => 'No extension', '1' => '+1 Month'],
    ];

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, name: string, labels: array<string, string>}
     */
    public function priceFor(array $selection): array
    {
        $plan = $selection['plan'] ?? '';

        if (! is_string($plan)) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        if (isset(self::FIXED_PLANS[$plan])) {
            return [
                'rupees' => self::FIXED_PLANS[$plan]['rupees'],
                'name' => self::FIXED_PLANS[$plan]['name'],
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

    public function fixedPriceFor(string $plan): ?int
    {
        return self::FIXED_PLANS[$plan]['rupees'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, name: string, labels: array<string, string>}
     */
    private function monthlyVirtualPrice(array $selection): array
    {
        $values = $this->validatedValues($selection, self::MONTHLY_LABELS);
        $isCouple = $values['couple'] === 'yes';
        $training = self::MONTHLY_BASE[$values['trainer']][$values['session']];

        if ($isCouple) {
            $training *= 1.5;
        }

        $nutrition = $values['nutrition'] === 'yes' ? 499 * ($isCouple ? 2 : 1) : 0;
        $extension = self::MONTHLY_EXTENSION[$isCouple ? 'couple' : 'solo'][$values['extension']];

        return [
            'rupees' => (int) round($training + $nutrition + $extension),
            'name' => 'Monthly Virtual Training',
            'labels' => $values['labels'],
        ];
    }

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, name: string, labels: array<string, string>}
     */
    private function twelveWeekVirtualPrice(array $selection): array
    {
        $values = $this->validatedValues($selection, self::PACKAGE_LABELS);
        $session = $values['session'];
        $trainer = $values['trainer'];

        if (! isset(self::VIRTUAL_12_WEEK_BASE[$session], self::PACKAGE_LABELS['trainer'][$trainer])) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        $training = self::VIRTUAL_12_WEEK_BASE[$session] + ($trainer === 'advanced' ? 1500 : 0);
        $training += $values['couple'] === 'yes' ? 3000 : 0;
        $nutrition = $values['nutrition'] === 'yes' ? 2500 : 0;
        $extension = $values['extension'] === '1' ? 1000 : 0;
        $labels = $values['labels'];
        $labels['program'] = $this->programLabel($selection);

        return [
            'rupees' => $training + $nutrition + $extension,
            'name' => $selection['plan'] === 'condition-12-weeks-virtual'
                ? $labels['program'].' 12 Weeks Virtual Training'
                : '12 Weeks Virtual Training',
            'labels' => $labels,
        ];
    }

    /**
     * @param  array<string, mixed>  $selection
     * @return array{rupees: int, name: string, labels: array<string, string>}
     */
    private function halfYearlyVirtualPrice(array $selection): array
    {
        $values = $this->validatedValues($selection, self::PACKAGE_LABELS);
        $session = $values['session'];
        $trainer = $values['trainer'];

        if (! isset(self::HALF_YEARLY_TRAINER[$trainer][$session])) {
            throw new InvalidArgumentException('Invalid plan selection.');
        }

        $training = (int) round(self::HALF_YEARLY_TRAINER[$trainer][$session] * 6 * 0.8);

        if ($values['couple'] === 'yes') {
            $training = (int) round($training * 1.5);
        }

        $nutrition = $values['nutrition'] === 'yes' ? 4500 : 0;
        $extension = $values['extension'] === '1' ? 2000 : 0;

        return [
            'rupees' => $training + $nutrition + $extension,
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
}
