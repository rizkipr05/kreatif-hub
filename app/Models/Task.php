<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'division',
        'priority',
        'status',
        'deadline',
        'created_by',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class)->latest();
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'Disetujui' && $this->deadline->isPast();
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match ($this->priority) {
            'Tinggi' => 'bg-rose-100 text-rose-700 border-rose-200',
            'Sedang' => 'bg-purple-100 text-purple-700 border-purple-200',
            'Rendah' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'Menunggu Review' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Dikerjakan' => 'bg-blue-100 text-blue-700 border-blue-200',
            'Revisi' => 'bg-rose-100 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}

