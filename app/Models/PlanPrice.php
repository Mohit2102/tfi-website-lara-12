<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    protected $fillable = [
        'key',
        'group',
        'label',
        'amount_inr',
        'amount_usd',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount_inr' => 'integer',
            'amount_usd' => 'float',
            'sort_order' => 'integer',
        ];
    }
}
