<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';

    public const SOURCE_RAZORPAY = 'razorpay';
    public const SOURCE_DUMMY = 'dummy';

    protected $fillable = [
        'user_id',
        'status',
        'source',
        'order_id',
        'payment_id',
        'amount_inr',
        'amount_usd',
        'currency',
        'plan_key',
        'plan_name',
        'selection',
        'labels',
    ];

    protected function casts(): array
    {
        return [
            'selection' => 'array',
            'labels' => 'array',
            'amount_usd' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): HasOne
    {
        return $this->hasOne(UserPlan::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
