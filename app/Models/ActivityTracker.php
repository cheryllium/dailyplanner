<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityTracker extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'last_completed_date',
        'frequency_days',
    ];

    protected $casts = [
        'last_completed_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue(): bool
    {
        if (!$this->frequency_days || !$this->last_completed_date) {
            return false;
        }

        $nextDueDate = $this->last_completed_date->copy()->addDays($this->frequency_days);
        $daysUntilDue = (int) now()->startOfDay()->diffInDays($nextDueDate->startOfDay(), false);
        return $daysUntilDue <= 0;
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $daysSinceCompleted = $this->last_completed_date->diffInDays(now()->startOfDay());
        return (int) ($daysSinceCompleted - $this->frequency_days);
    }
}
