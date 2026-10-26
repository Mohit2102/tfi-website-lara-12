<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlanPrice;
use App\Services\PlanPriceRepository;
use App\Support\PlanPriceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanPriceController extends Controller
{
    public function edit(PlanPriceRepository $prices): View
    {
        $rows = collect($prices->editorRows())->groupBy('group');

        return view('superadmin.prices.edit', compact('rows'));
    }

    public function update(Request $request, PlanPriceRepository $prices): RedirectResponse
    {
        $data = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*.amount_inr' => ['required', 'integer', 'min:0', 'max:10000000'],
            'prices.*.amount_usd' => ['required', 'numeric', 'min:0', 'max:1000000'],
        ]);

        $definitions = PlanPriceCatalog::definitions();

        foreach ($data['prices'] as $key => $amounts) {
            if (! is_string($key) || ! isset($definitions[$key])) {
                continue;
            }

            PlanPrice::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => $definitions[$key]['group'],
                    'label' => $definitions[$key]['label'],
                    'amount_inr' => (int) $amounts['amount_inr'],
                    'amount_usd' => round((float) $amounts['amount_usd'], 2),
                    'sort_order' => $definitions[$key]['sort'],
                ]
            );
        }

        $prices->forget();

        return redirect()
            ->route('superadmin.prices.edit')
            ->with('success', 'Plan prices saved in INR and USD.');
    }
}
