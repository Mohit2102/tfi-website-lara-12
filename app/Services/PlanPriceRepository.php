<?php

namespace App\Services;

use App\Models\PlanPrice;
use App\Support\PlanPriceCatalog;
use Illuminate\Support\Collection;
use Throwable;

class PlanPriceRepository
{
    /** @var Collection<string, PlanPrice>|null */
    private ?Collection $rows = null;

    /**
     * @return Collection<string, PlanPrice>
     */
    public function all(): Collection
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        try {
            $this->rows = PlanPrice::query()->orderBy('sort_order')->get()->keyBy('key');
        } catch (Throwable) {
            $this->rows = collect();
        }

        return $this->rows;
    }

    public function forget(): void
    {
        $this->rows = null;
    }

    public function inr(string $key, int $default): int
    {
        $row = $this->all()->get($key);

        return $row ? (int) $row->amount_inr : $default;
    }

    public function usd(string $key, float $default = 0): float
    {
        $row = $this->all()->get($key);

        return $row ? (float) $row->amount_usd : $default;
    }

    /**
     * @return list<array{key: string, group: string, label: string, amount_inr: int, amount_usd: float}>
     */
    public function editorRows(): array
    {
        $stored = $this->all();
        $rows = [];

        foreach (PlanPriceCatalog::definitions() as $key => $definition) {
            $row = $stored->get($key);
            $rows[] = [
                'key' => $key,
                'group' => $definition['group'],
                'label' => $row->label ?? $definition['label'],
                'amount_inr' => $row ? (int) $row->amount_inr : $definition['inr'],
                'amount_usd' => $row ? (float) $row->amount_usd : 0.0,
            ];
        }

        return $rows;
    }
}
