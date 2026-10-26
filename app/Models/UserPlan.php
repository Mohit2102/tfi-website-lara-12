<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlan extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'trainer_id',
        'name',
        'plan_key',
        'amount_inr',
        'labels',
        'payment_id',
        'total_weeks',
        'starts_on',
        'status',
        'sessions_per_week',
        'cardio_target',
        'strength_target',
    ];

    protected function casts(): array
    {
        return [
            'labels' => 'array',
            'starts_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function currentWeek(): int
    {
        $weeksIn = (int) floor($this->starts_on->diffInDays(now(), false) / 7) + 1;

        return max(1, min((int) $this->total_weeks, $weeksIn));
    }
}
