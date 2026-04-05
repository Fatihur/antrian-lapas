<?php

namespace App\Services;

use App\Models\VisitQueue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfTicketService
{
    public function generate(VisitQueue $queue): string
    {
        $queue->load(['session', 'followers', 'verifiedBy']);

        $data = [
            'queue' => $queue,
            'session' => $queue->session,
            'followers' => $queue->followers,
            'logo_path' => public_path('images/logo-lapas.png'),
            'institution_name' => 'Lembaga Pemasyarakatan Kelas IIA Sumbawa',
            'institution_address' => 'Jl. Lintas Sumbawa No. 123, Sumbawa Besar',
            'generated_at' => now()->format('d F Y H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.ticket', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $filename = 'tickets/'.$queue->kode_booking.'_'.$queue->nomor_antrian.'.pdf';
        $fullPath = storage_path('app/public/'.$filename);

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->save($fullPath);

        return $filename;
    }

    public function regenerate(VisitQueue $queue): string
    {
        $existingPath = $queue->pdf_path;

        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        return $this->generate($queue);
    }

    public function download(VisitQueue $queue)
    {
        $queue->load(['session', 'followers']);

        $data = [
            'queue' => $queue,
            'session' => $queue->session,
            'followers' => $queue->followers,
            'logo_path' => public_path('images/logo-lapas.png'),
            'institution_name' => 'Lembaga Pemasyarakatan Kelas IIA Sumbawa',
            'institution_address' => 'Jl. Lintas Sumbawa No. 123, Sumbawa Besar',
            'generated_at' => now()->format('d F Y H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.ticket', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Bukti_Antrian_'.$queue->nomor_antrian.'.pdf';

        return $pdf->download($filename);
    }

    public function stream(VisitQueue $queue)
    {
        $queue->load(['session', 'followers']);

        $data = [
            'queue' => $queue,
            'session' => $queue->session,
            'followers' => $queue->followers,
            'logo_path' => public_path('images/logo-lapas.png'),
            'institution_name' => 'Lembaga Pemasyarakatan Kelas IIA Sumbawa',
            'institution_address' => 'Jl. Lintas Sumbawa No. 123, Sumbawa Besar',
            'generated_at' => now()->format('d F Y H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.ticket', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Bukti_Antrian_'.$queue->nomor_antrian.'.pdf');
    }
}
