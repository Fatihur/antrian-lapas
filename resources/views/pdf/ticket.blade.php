<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Antrian Kunjungan - {{ $queue->nomor_antrian }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0f766e;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .institution-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 5px;
        }
        
        .institution-address {
            font-size: 10px;
            color: #666;
        }
        
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #0f766e;
            text-transform: uppercase;
        }
        
        .ticket-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .queue-number {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #0f766e;
            margin: 10px 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 5px 10px 5px 0;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
            color: #333;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #0f766e;
        }
        
        .follower-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .follower-table th {
            background: #0f766e;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        
        .follower-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        
        .follower-table tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .summary-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0;
        }
        
        .summary-title {
            font-weight: bold;
            color: #166534;
            margin-bottom: 5px;
        }
        
        .instructions {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 6px;
            padding: 12px;
            margin-top: 20px;
        }
        
        .instructions-title {
            font-weight: bold;
            color: #9a3412;
            margin-bottom: 8px;
        }
        
        .instructions-list {
            margin-left: 15px;
            font-size: 11px;
        }
        
        .instructions-list li {
            margin-bottom: 4px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .generated-at {
            font-size: 9px;
            color: #999;
            text-align: right;
            margin-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-menunggu { background: #fef3c7; color: #92400e; }
        .status-disetujui { background: #dcfce7; color: #166534; }
        .status-ditolak { background: #fee2e2; color: #991b1b; }
        .status-dipanggil { background: #dbeafe; color: #1e40af; }
        .status-selesai { background: #e0e7ff; color: #3730a3; }
        .status-kedaluwarsa { background: #f3f4f6; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists($logo_path))
                <img src="{{ $logo_path }}" alt="Logo" class="logo">
            @endif
            <div class="institution-name">{{ $institution_name }}</div>
            <div class="institution-address">{{ $institution_address }}</div>
        </div>
        
        <div class="title">Bukti Antrian Kunjungan</div>
        
        <div class="ticket-info">
            <div class="queue-number">{{ $queue->nomor_antrian }}</div>
            
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Kode Booking:</div>
                    <div class="info-value"><strong>{{ $queue->kode_booking }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Daftar:</div>
                    <div class="info-value">{{ $queue->waktu_daftar->format('d F Y H:i') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Kunjungan:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($queue->tanggal_kunjungan)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Sesi:</div>
                    <div class="info-value">{{ $session->nama_sesi }} ({{ $session->jam_buka->format('H:i') }} - {{ $session->jam_tutup->format('H:i') }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $queue->status_antrian)) }}">
                            {{ $queue->status_antrian }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section-title">Data Pengunjung Utama</div>
        
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Lengkap:</div>
                <div class="info-value">{{ $queue->nama_pengunjung }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Identitas:</div>
                <div class="info-value">{{ $queue->jenis_identitas }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Identitas:</div>
                <div class="info-value">{{ $queue->nik_pendaftar }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor HP:</div>
                <div class="info-value">{{ $queue->no_hp }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Hubungan dengan WBP:</div>
                <div class="info-value">{{ $queue->hubungan_wbp }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama WBP:</div>
                <div class="info-value">{{ $queue->nama_wbp }}</div>
            </div>
        </div>
        
        @if($followers->count() > 0)
        <div class="section-title">Data Pengikut ({{ $followers->count() }} orang)</div>
        
        <table class="follower-table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 40%;">Nama Lengkap</th>
                    <th style="width: 30%;">Nomor Identitas</th>
                    <th style="width: 20%;">Jenis Kelamin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($followers as $index => $follower)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $follower->nama_pengikut }}</td>
                    <td>{{ $follower->nomor_identitas_pengikut }}</td>
                    <td>{{ $follower->jenis_kelamin_pengikut }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="section-title">Data Pengikut</div>
        <p style="color: #666; font-style: italic;">Tidak ada pengikut yang didaftarkan.</p>
        @endif
        
        <div class="summary-box">
            <div class="summary-title">Ringkasan Kunjungan</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Total Pengikut:</div>
                    <div class="info-value">{{ $followers->count() }} orang</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Pengunjung:</div>
                    <div class="info-value">{{ $followers->count() + 1 }} orang (termasuk pengunjung utama)</div>
                </div>
            </div>
        </div>
        
        @if($queue->catatan)
        <div class="section-title">Catatan</div>
        <p>{{ $queue->catatan }}</p>
        @endif
        
        <div class="instructions">
            <div class="instructions-title">Instruksi Kedatangan:</div>
            <ol class="instructions-list">
                <li>Datang 30 menit sebelum sesi kunjungan dimulai</li>
                <li>Bawa kartu identitas asli yang didaftarkan (KTP/SIM/Paspor/KK)</li>
                <li>Tunjukkan bukti antrian ini (cetakan atau digital) kepada petugas</li>
                <li>Ikuti prosedur keamanan dan pemeriksaan di loket masuk</li>
                <li>Dilarang membawa barang-barang terlarang sesuai peraturan lapas</li>
                <li>Patuhi protokol kesehatan dan aturan kunjungan yang berlaku</li>
            </ol>
        </div>
        
        <div class="footer">
            <p>Dokumen ini sah dan dapat digunakan sebagai bukti antrian kunjungan.</p>
            <p>Jika ada pertanyaan, hubungi petugas loket atau call center Lapas Sumbawa.</p>
        </div>
        
        <div class="generated-at">
            Dokumen ini dicetak pada: {{ $generated_at }}
        </div>
    </div>
</body>
</html>
