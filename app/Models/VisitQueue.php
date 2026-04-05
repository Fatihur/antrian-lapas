<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitQueue extends Model
{
    use HasFactory;

    protected $table = 'visit_queues';

    protected $fillable = [
        'visit_schedule_id',
        'visit_session_id',
        'tanggal_kunjungan',
        'kode_booking',
        'nomor_antrian',
        'nik_pendaftar',
        'jenis_identitas',
        'nama_pengunjung',
        'no_hp',
        'hubungan_wbp',
        'nama_wbp',
        'foto_identitas',
        'catatan',
        'status_antrian',
        'pdf_path',
        'alasan_penolakan',
        'waktu_daftar',
        'waktu_verifikasi',
        'verified_by',
        'waktu_selesai',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'waktu_daftar' => 'datetime',
        'waktu_verifikasi' => 'datetime',
        'waktu_selesai' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(VisitSchedule::class, 'visit_schedule_id');
    }

    public function session()
    {
        return $this->belongsTo(VisitSession::class, 'visit_session_id');
    }

    public function followers()
    {
        return $this->hasMany(VisitFollower::class, 'visit_queue_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(QueueStatusLog::class, 'visit_queue_id');
    }

    public function calls()
    {
        return $this->hasMany(QueueCall::class, 'visit_queue_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function getTotalPengikutAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getTotalOrangAttribute(): int
    {
        return $this->total_pengikut + 1;
    }

    public function getTanggalKunjunganDisplayAttribute()
    {
        return $this->tanggal_kunjungan?->format('d F Y') ?? $this->schedule?->tanggal?->format('d F Y');
    }

    public function getSesiKunjunganAttribute()
    {
        return $this->schedule?->sesi ?? '-';
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_kunjungan', today())
            ->orWhere(function ($q) {
                $q->whereNull('tanggal_kunjungan')
                    ->whereHas('schedule', function ($sq) {
                        $sq->whereDate('tanggal', today());
                    });
            });
    }

    public function isStatus($status): bool
    {
        return $this->status_antrian === $status;
    }

    public function canBeCalled(): bool
    {
        return $this->status_antrian === 'Disetujui' || $this->status_antrian === 'Menunggu Dipanggil';
    }

    public function canBeVerified(): bool
    {
        return false; // Tidak ada proses verifikasi, langsung disetujui
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_antrian', [
            'Disetujui',
            'Menunggu Dipanggil',
            'Dipanggil',
        ]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_antrian', $status);
    }

    public function scopeByNik($query, $nik)
    {
        return $query->where('nik_pendaftar', $nik);
    }
}
