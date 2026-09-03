<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul ?? 'Laporan Hasil Ujian' }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            color: #333;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
        }
        .header h2 {
            text-transform: uppercase;
            font-size: 16px;
            margin: 2px 0;
        }
        .header p {
            margin: 2px 0;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        table th, table td { 
            border: 1px solid #000; 
            padding: 5px 4px; 
        }
        table th { 
            background-color: #f2f2f2; 
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-success { color: #28a745; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #856404; font-weight: bold; }
        .text-primary { color: #0066cc; font-weight: bold; }
        .text-secondary { color: #6c757d; font-weight: bold; }
        
        .bg-success-light { background-color: #d4edda !important; }
        .bg-danger-light { background-color: #f8d7da !important; }
        .bg-warning-light { background-color: #fff3cd !important; }
        .bg-primary-light { background-color: #cce5ff !important; }
        .bg-secondary-light { background-color: #e2e3e5 !important; }

        .footer { 
            margin-top: 30px;
            text-align: right;
        }
        .footer .ttd {
            width: 250px;
            margin-left: auto;
            text-align: center;
        }
        @media print {
            @page { margin: 15mm; size: A4 landscape; }
            body { -webkit-print-color-adjust: exact; }
        }
        .info-export {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
            padding: 5px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .info-export span {
            margin-right: 15px;
        }
        .badge-status {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN HASIL UJIAN</h2>
        <p><strong>{{ $judul ?? 'Hasil Ujian' }}</strong></p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div class="info-export">
        <span><strong>Mata Pelajaran:</strong> {{ $mapel_nama ?? '-' }}</span>
        <span><strong>Kelas:</strong> {{ $kelas_nama ?? '-' }}</span>
        <span><strong>Jumlah Siswa:</strong> {{ $data->count() ?? 0 }}</span>
        <span><strong>Tanggal Ujian:</strong> {{ $tanggal_ujian ?? date('d-m-Y') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Siswa</th>
                <th width="10%">NIS</th>
                <th width="10%">NISN</th>
                <th width="12%">Kelas</th>
                <th width="8%">Benar</th>
                <th width="8%">Dijawab</th>
                <th width="8%">Total Soal</th>
                <th width="10%">Nilai</th>
                <th width="16%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $rowClass = '';
                    $textClass = 'text-primary';
                    
                    if($item->status_label == 'SELESAI') {
                        $rowClass = 'bg-success-light';
                        $textClass = 'text-success';
                    } elseif($item->status_label == 'DITINGGALKAN') {
                        $rowClass = 'bg-danger-light';
                        $textClass = 'text-danger';
                    } elseif($item->status_label == 'WAKTU HABIS') {
                        $rowClass = 'bg-warning-light';
                        $textClass = 'text-warning';
                    } elseif($item->status_label == 'MENGERJAKAN') {
                        $rowClass = 'bg-primary-light';
                        $textClass = 'text-primary';
                    } else {
                        $rowClass = 'bg-secondary-light';
                        $textClass = 'text-secondary';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_siswa ?? '-' }}</td>
                    <td class="text-center">{{ $item->nis ?? '-' }}</td>
                    <td class="text-center">{{ $item->nisn ?? '-' }}</td>
                    <td class="text-center">{{ $item->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $item->benar ?? 0 }}</td>
                    <td class="text-center">{{ $item->dijawab ?? 0 }}</td>
                    <td class="text-center">{{ $item->total_soal ?? 0 }}</td>
                    <td class="text-center text-bold">
                        {{ isset($item->nilai) ? number_format($item->nilai, 2) : '0.00' }}
                    </td>
                    <td class="text-center {{ $textClass }}">
                        <span class="badge-status">{{ $item->status_label ?? 'BELUM UJIAN' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
                        <strong>Tidak ada data ujian untuk kriteria yang dipilih.</strong>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="5" class="text-center">RATA-RATA</td>
                <td class="text-center">
                    {{ $data->avg('benar') ? number_format($data->avg('benar'), 2) : '0.00' }}
                </td>
                <td class="text-center">
                    {{ $data->avg('dijawab') ? number_format($data->avg('dijawab'), 2) : '0.00' }}
                </td>
                <td class="text-center">
                    {{ $data->avg('total_soal') ? number_format($data->avg('total_soal'), 2) : '0.00' }}
                </td>
                <td class="text-center">
                    {{ $data->avg('nilai') ? number_format($data->avg('nilai'), 2) : '0.00' }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 10px; font-size: 9px; color: #666;">
        <p><strong>Keterangan Status:</strong></p>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="display: inline-block; margin-right: 15px;">
                <span style="color: #28a745; font-weight: bold;">● SELESAI</span> - Peserta telah menyelesaikan ujian
            </li>
            <li style="display: inline-block; margin-right: 15px;">
                <span style="color: #0066cc; font-weight: bold;">● MENGERJAKAN</span> - Peserta sedang mengerjakan ujian
            </li>
            <li style="display: inline-block; margin-right: 15px;">
                <span style="color: #856404; font-weight: bold;">● WAKTU HABIS</span> - Waktu ujian telah habis
            </li>
            <li style="display: inline-block; margin-right: 15px;">
                <span style="color: #dc3545; font-weight: bold;">● DITINGGALKAN</span> - Peserta meninggalkan ujian
            </li>
        </ul>
    </div>

    <div class="footer">
        <div class="ttd">
            <p>Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>KEPALA DINAS</strong></p>
            <br><br><br>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">SATRIA WIBAWA, S.Sos</p>
            <p>NIP. 19750101 200003 1 001</p>
        </div>
    </div>

    <div style="clear: both;"></div>
</body>
</html>