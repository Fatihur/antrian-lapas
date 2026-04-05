<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class VisitSession extends Model
{
    use HasFactory;

    protected $table = 'visit_sessions';

    protected $fillable = [
        'nama_sesi',
        'kode_sesi',
        'jam_buka',
        'jam_tutup',
        'kuota_sesi',
        'is_active',
        'urutan',
        'keterangan',
    ];

    protected $casts = [
        'jam_buka' => 'datetime:H:i',
        'jam_tutup' => 'datetime:H:i',
        'kuota_sesi' => 'integer',
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all active sessions ordered by urutan
     */
    public static function getActiveSessions(): Collection
    {
        return self::where('is_active', true)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Get all sessions (including inactive) ordered by urutan
     */
    public static function getAllSessions(): Collection
    {
        return self::orderBy('urutan')->get();
    }

    /**
     * Check if current time is within operational hours for this session
     */
    public function isJamOperasional(?Carbon $time = null): bool
    {
        $time = $time ?? now();
        $currentTime = $time->format('H:i:s');

        // Check if before opening or after closing
        if ($currentTime < $this->jam_buka->format('H:i:s') ||
            $currentTime > $this->jam_tutup->format('H:i:s')) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted operational hours info
     */
    public function getInfoOperasional(): string
    {
        $jamBuka = $this->jam_buka->format('H:i');
        $jamTutup = $this->jam_tutup->format('H:i');

        return "{$jamBuka} - {$jamTutup}";
    }

    /**
     * Get display name with operational hours
     */
    public function getDisplayName(): string
    {
        return "{$this->nama_sesi} ({$this->getInfoOperasional()})";
    }

    /**
     * Relationship with VisitQueue
     */
    public function queues()
    {
        return $this->hasMany(VisitQueue::class, 'visit_session_id');
    }

    /**
     * Scope for active sessions only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
