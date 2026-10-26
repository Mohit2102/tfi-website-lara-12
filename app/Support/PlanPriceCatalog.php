<?php

namespace App\Support;

class PlanPriceCatalog
{
    /**
     * Editable price points used by checkout and the public plan builders.
     *
     * @return array<string, array{group: string, label: string, inr: int, sort: int}>
     */
    public static function definitions(): array
    {
        $rows = [
            'fixed.monthly-nv' => ['group' => 'Fixed non-virtual plans', 'label' => 'Monthly Non-Virtual Training', 'inr' => 5999],
            'fixed.12-weeks-nv' => ['group' => 'Fixed non-virtual plans', 'label' => '12 Weeks Non-Virtual Transformation', 'inr' => 7499],
            'fixed.half-yearly-nv' => ['group' => 'Fixed non-virtual plans', 'label' => 'Half Yearly Non-Virtual Transformation', 'inr' => 27999],

            'monthly.base.specialized.12' => ['group' => 'Monthly virtual — session rates (solo)', 'label' => 'Specialized · 12 sessions', 'inr' => 15000],
            'monthly.base.specialized.16' => ['group' => 'Monthly virtual — session rates (solo)', 'label' => 'Specialized · 16 sessions', 'inr' => 20000],
            'monthly.base.specialized.20' => ['group' => 'Monthly virtual — session rates (solo)', 'label' => 'Specialized · 20 sessions', 'inr' => 25000],
            'monthly.base.certified.12' => ['group' => 'Monthly virtual — session rates (solo)', 'label' => 'Certified · 12 sessions', 'inr' => 9999],
            'monthly.base.certified.16' => ['group' => 'Monthly virtual — session rates (solo)', 'label' => 'Certified · 16 sessions', 'inr' => 13332],
            'monthly.base.certified.20' => ['group' => 'Monthly virtual — session rates (solo)', 'label' => 'Certified · 20 sessions', 'inr' => 16665],
            'monthly.nutrition' => ['group' => 'Monthly virtual — add-ons', 'label' => 'Nutrition (per person / month)', 'inr' => 499],
            'monthly.extension.solo.10' => ['group' => 'Monthly virtual — add-ons', 'label' => '10-day extension · solo', 'inr' => 999],
            'monthly.extension.solo.30' => ['group' => 'Monthly virtual — add-ons', 'label' => '30-day extension · solo', 'inr' => 2499],
            'monthly.extension.couple.10' => ['group' => 'Monthly virtual — add-ons', 'label' => '10-day extension · couple', 'inr' => 1499],
            'monthly.extension.couple.30' => ['group' => 'Monthly virtual — add-ons', 'label' => '30-day extension · couple', 'inr' => 3499],

            '12w.base.3' => ['group' => '12-week virtual', 'label' => '3 sessions / week base', 'inr' => 7999],
            '12w.base.4' => ['group' => '12-week virtual', 'label' => '4 sessions / week base', 'inr' => 9999],
            '12w.base.5' => ['group' => '12-week virtual', 'label' => '5 sessions / week base', 'inr' => 11999],
            '12w.trainer.advanced' => ['group' => '12-week virtual', 'label' => 'Certified trainer upgrade', 'inr' => 1500],
            '12w.couple' => ['group' => '12-week virtual', 'label' => 'Couple add-on', 'inr' => 3000],
            '12w.nutrition' => ['group' => '12-week virtual', 'label' => 'Nutrition add-on', 'inr' => 2500],
            '12w.extension' => ['group' => '12-week virtual', 'label' => '+1 month extension', 'inr' => 1000],

            'hy.trainer.standard.3' => ['group' => 'Half-yearly virtual — monthly rates (solo)', 'label' => 'Specialized · 3 sessions / week', 'inr' => 15000],
            'hy.trainer.standard.4' => ['group' => 'Half-yearly virtual — monthly rates (solo)', 'label' => 'Specialized · 4 sessions / week', 'inr' => 20000],
            'hy.trainer.standard.5' => ['group' => 'Half-yearly virtual — monthly rates (solo)', 'label' => 'Specialized · 5 sessions / week', 'inr' => 25000],
            'hy.trainer.advanced.3' => ['group' => 'Half-yearly virtual — monthly rates (solo)', 'label' => 'Certified · 3 sessions / week', 'inr' => 9999],
            'hy.trainer.advanced.4' => ['group' => 'Half-yearly virtual — monthly rates (solo)', 'label' => 'Certified · 4 sessions / week', 'inr' => 13332],
            'hy.trainer.advanced.5' => ['group' => 'Half-yearly virtual — monthly rates (solo)', 'label' => 'Certified · 5 sessions / week', 'inr' => 16665],
            'hy.nutrition' => ['group' => 'Half-yearly virtual — add-ons', 'label' => 'Nutrition add-on (6 months)', 'inr' => 4500],
            'hy.extension' => ['group' => 'Half-yearly virtual — add-ons', 'label' => '+1 month extension', 'inr' => 2000],
        ];

        $sort = 0;
        foreach ($rows as $key => $row) {
            $rows[$key]['sort'] = $sort++;
        }

        return $rows;
    }

    /**
     * @return array<string, string>
     */
    public static function pageTitles(): array
    {
        return [
            'index' => 'Home',
            'about' => 'About',
            'trainers' => 'Trainers',
            'calculator' => 'Calculator',
            'payment-success' => 'Payment success',
            'virtual-training' => 'Virtual training',
            'non-virtual-training' => 'Non-virtual training',
            'personal-training' => 'Personal training',
            'monthly-virtual-training' => 'Monthly virtual training',
            '12-weeks-virtual-training' => '12 weeks virtual training',
            'half-yearly-virtual-training' => 'Half yearly virtual training',
            'monthly-non-virtual-training' => 'Monthly non-virtual training',
            '12-weeks-non-virtual-training' => '12 weeks non-virtual training',
            'half-yearly-non-virtual-training' => 'Half yearly non-virtual training',
            'diabetes-management' => 'Diabetes management',
            'pcos-pcod-nutrition' => 'PCOS / PCOD nutrition',
            'hypertension-heart-healthy' => 'Hypertension / heart-healthy',
            'prenatal-postnatal-fitness' => 'Prenatal / postnatal fitness',
            'back-spine-posture' => 'Back, spine & posture',
            'cardiovascular-conditioning' => 'Cardiovascular conditioning',
            'thyroid-metabolic-support' => 'Thyroid / metabolic support',
            'gerd-support' => 'GERD support',
        ];
    }
}
