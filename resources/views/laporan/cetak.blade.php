<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            color: #333;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            position: relative;
        }
        .header img { 
            width: 70px; 
            position: absolute; 
            left: 10px; 
            top: 0;
        }
        .header h3, .header h2, .header p { 
            margin: 2px 0; 
        }
        .header h2 {
            text-transform: uppercase;
            font-size: 18px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        table th, table td { 
            border: 1px solid #000; 
            padding: 6px 4px; 
        }
        table th { 
            background-color: #f2f2f2; 
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        /* Pewarnaan Status Spesifik untuk Hasil Cetak */
        .status-expired { color: #dc3545 !important; font-weight: bold; }
        .status-warning { color: #856404 !important; font-weight: bold; }
        .status-active { color: #28a745 !important; font-weight: bold; }
        
        /* Shading baris agar lebih mudah dibaca saat kertas diprint */
        .bg-danger-light { background-color: #f8d7da !important; }
        .bg-warning-light { background-color: #fff3cd !important; }

        .footer { 
            width: 250px; 
            float: right; 
            text-align: center; 
            margin-top: 30px; 
        }
        @media print {
            @page { margin: 15mm; size: A4; }
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <img src="{{ asset('assets/img/logo_disdik.png') }}" alt="Logo">
        <h2>PEMERINTAH KOTA CIREBON</h2>
        <h3>DINAS PENDIDIKAN DAN KEBUDAYAAN</h3>
        <p>Jl. Brigjen Dharsono By Pass No. 7, Sunyaragi, Kesambi, Kota Cirebon, Jawa Barat 45132</p>
        <p>Telp. (0231) 486579</p>
    </div>

    <h3 class="text-center" style="margin-bottom: 20px;">{{ strtoupper($title) }}</h3>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">NPSN</th>
                <th>Nama Lembaga</th>
                <th width="10%">Jenis</th>
                <th width="15%">Pengelola</th>
                <th width="15%">No. Sertifikat</th>
                <th width="12%">Masa Berlaku</th>
                <th width="15%">Status Izin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                @php
                    $rowClass = '';
                    $textClass = '';
                    
                    // Menggunakan properti status_label yang dikirim dari LaporanController
                    if($item->status_label == 'danger') {
                        $rowClass = 'bg-danger-light';
                        $textClass = 'status-expired';
                    } elseif($item->status_label == 'warning') {
                        $rowClass = 'bg-warning-light';
                        $textClass = 'status-warning';
                    } else {
                        $textClass = 'status-active';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $item->npsn }}</td>
                    <td>{{ $item->nama_lembaga }}</td>
                    <td class="text-center">{{ $item->jenis->nama ?? '-' }}</td>
                    <td>{{ $item->pengelola }}</td>
                    <td>{{ $item->izin->no_sertifikat ?? '-' }}</td>
                    <td class="text-center">
                        {{ $item->izin && $item->izin->masa_berlaku ? \Carbon\Carbon::parse($item->izin->masa_berlaku)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center {{ $textClass }}">
                        {{ strtoupper($item->status_teks) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data tersedia untuk kriteria ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Kota Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p><strong>KEPALA DINAS</strong></p>
        <br><br><br><br>
        <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">SATRIA WIBAWA, S.Sos</p>
        <p>NIP. 19750101 200003 1 001</p>
    </div>

    <div style="clear: both;"></div>
</body>
</html>