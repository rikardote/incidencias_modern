<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemNotification extends Model
{
    protected $fillable = ['sender_id', 'target_user_id', 'title', 'body', 'type'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(SystemNotificationRead::class, 'notification_id');
    }

    /**
     * Verifica si un usuario ya leyó esta notificación.
     */
    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    /**
     * Scope: notificaciones visibles para un usuario
     * (las globales + las dirigidas específicamente a él)
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('target_user_id')
              ->orWhere('target_user_id', $userId);
        });
    }

    /**
     * Scope: notificaciones NO leídas por un usuario
     */
    public function scopeUnreadByUser($query, int $userId)
    {
        return $query->forUser($userId)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId));
    }
}
