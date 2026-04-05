<?php

namespace App\Services;

use App\Models\VisitQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QueueNumberGenerator
{
    public function generate(string $tanggalKunjungan, string $kodeSesi): string
    {
        return DB::transaction(function () use ($tanggalKunjungan, $kodeSesi) {
            $tanggal = Carbon::parse($tanggalKunjungan);
            $tanggalFormatted = $tanggal->format('dmY');

            $latestQueue = VisitQueue::whereDate('tanggal_kunjungan', $tanggalKunjungan)
                ->whereHas('session', function ($query) use ($kodeSesi) {
                    $query->where('kode_sesi', $kodeSesi);
                })
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            $prefix = 'A';

            if ($latestQueue) {
                $parts = explode('-', $latestQueue->nomor_antrian);
                if (count($parts) >= 3) {
                    $currentPrefix = substr($parts[0], 0, 1);
                    $currentNumber = (int) substr($parts[0], 1);

                    if ($currentNumber >= 999) {
                        $nextNumber = 1;
                        $prefix = $this->getNextPrefix($currentPrefix);
                    } else {
                        $nextNumber = $currentNumber + 1;
                        $prefix = $currentPrefix;
                    }
                }
            }

            $nomorPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $nomorAntrian = "{$prefix}{$nomorPadded}-{$kodeSesi}-{$tanggalFormatted}";

            $attempts = 0;
            $maxAttempts = 100;

            while ($this->exists($nomorAntrian) && $attempts < $maxAttempts) {
                $nextNumber++;
                if ($nextNumber > 999) {
                    $nextNumber = 1;
                    $prefix = $this->getNextPrefix($prefix);
                }
                $nomorPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $nomorAntrian = "{$prefix}{$nomorPadded}-{$kodeSesi}-{$tanggalFormatted}";
                $attempts++;
            }

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Unable to generate unique queue number after maximum attempts');
            }

            return $nomorAntrian;
        });
    }

    private function getNextPrefix(string $currentPrefix): string
    {
        $prefixes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $currentIndex = array_search($currentPrefix, $prefixes);

        if ($currentIndex === false || $currentIndex >= count($prefixes) - 1) {
            return 'A';
        }

        return $prefixes[$currentIndex + 1];
    }

    private function exists(string $nomorAntrian): bool
    {
        return VisitQueue::where('nomor_antrian', $nomorAntrian)->exists();
    }

    public function generateBookingCode(): string
    {
        $bookingCode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));

        $attempts = 0;
        $maxAttempts = 100;

        while (VisitQueue::where('kode_booking', $bookingCode)->exists() && $attempts < $maxAttempts) {
            $bookingCode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
            $attempts++;
        }

        if ($attempts >= $maxAttempts) {
            throw new \RuntimeException('Unable to generate unique booking code');
        }

        return $bookingCode;
    }
}
