<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachMessage extends Model
{
    public const FROM_TRAINEE = 'trainee';
    public const FROM_TRAINER = 'trainer';

    protected $fillable = [
        'trainee_id',
        'trainer_id',
        'body',
        'from_role',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function isUnread(): bool
    {
        return $this->read_at === null && $this->from_role === self::FROM_TRAINEE;
    }

    public function isFromTrainer(): bool
    {
        return $this->from_role === self::FROM_TRAINER;
    }
}
