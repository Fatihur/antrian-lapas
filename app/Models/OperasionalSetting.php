<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OperasionalSetting extends Model
{
    use HasFactory;

    protected $table = 'operasional_settings';

    protected $fillable = [
        'status_default',
        'hari_libur_mingguan',
        'tanggal_libur_khusus',
    ];

    protected $casts = [
        'hari_libur_mingguan' => 'array',
        'tanggal_libur_khusus' => 'array',
    ];

    /**
     * Get the single settings record (singleton pattern)
     */
    public static function getSettings(): ?self
    {
        return self::first();
    }

    /**
     * Check if a date is a holiday (libur)
     */
    public function isHariLibur(Carbon $date): bool
    {
        $dayName = $date->locale('id')->dayName;

        // Check weekly holidays
        $weeklyHolidays = $this->hari_libur_mingguan ?? [];
        if (in_array($dayName, $weeklyHolidays)) {
            return true;
        }

        // Check special holidays
        $specialHolidays = $this->tanggal_libur_khusus ?? [];
        $dateString = $date->format('Y-m-d');

        foreach ($specialHolidays as $holiday) {
            if (is_array($holiday) && $holiday['tanggal'] === $dateString) {
                return true;
            }
            if (is_string($holiday) && $holiday === $dateString) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get list of weekly holiday names
     */
    public function getHariLiburMingguanNames(): array
    {
        return $this->hari_libur_mingguan ?? [];
    }

    /**
     * Get list of special holiday dates
     */
    public function getTanggalLiburKhusus(): array
    {
        return $this->tanggal_libur_khusus ?? [];
    }

    /**
     * Get active sessions with their info
     */
    public static function getActiveSessionsWithInfo(): array
    {
        $sessions = VisitSession::getActiveSessions();
        $result = [];

        foreach ($sessions as $session) {
            $result[$session->kode_sesi] = [
                'id' => $session->id,
                'nama' => $session->nama_sesi,
                'kode' => $session->kode_sesi,
                'jam' => $session->getInfoOperasional(),
                'kuota' => $session->kuota_sesi,
            ];
        }

        return $result;
    }
}
