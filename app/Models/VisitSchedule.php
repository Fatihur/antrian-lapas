<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitSchedule extends Model
{
    use HasFactory;

    protected $table = 'visit_schedules';

    protected $fillable = [
        'tanggal',
        'sesi',
        'kuota_maksimal',
        'kuota_terpakai',
        'status_jadwal',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'kuota_maksimal' => 'integer',
        'kuota_terpakai' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function queues()
    {
        return $this->hasMany(VisitQueue::class, 'visit_schedule_id');
    }

    public function getSisaKuotaAttribute(): int
    {
        return $this->kuota_maksimal - $this->kuota_terpakai;
    }

    public function isKuotaAvailable(): bool
    {
        return $this->status_jadwal === 'buka' && $this->sisa_kuota > 0;
    }

    public function incrementKuotaTerpakai()
    {
        $this->increment('kuota_terpakai');
    }

    public function decrementKuotaTerpakai()
    {
        $this->decrement('kuota_terpakai');
    }

    public function scopeOpen($query)
    {
        return $query->where('status_jadwal', 'buka');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('tanggal', $date);
    }

    public function scopeForSession($query, $session)
    {
        return $query->where('sesi', $session);
    }
}
