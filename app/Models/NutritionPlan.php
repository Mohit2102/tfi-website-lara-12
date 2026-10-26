<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionPlan extends Model
{
    protected $fillable = [
        'trainee_id',
        'trainer_id',
        'title',
        'path',
        'original_name',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function url(): string
    {
        return asset($this->path);
    }

    public function deleteStoredFile(): void
    {
        if (is_string($this->path) && str_starts_with($this->path, 'uploads/nutrition/') && is_file(public_path($this->path))) {
            @unlink(public_path($this->path));
        }
    }
}
