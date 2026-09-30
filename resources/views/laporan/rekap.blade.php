@php
    $isPdfMode = ! empty($isPdf);
    // Browser pratinjau pakai URL, dompdf pakai path file lokal.
    $srcKanan = $isPdfMode ? $logoKanan : asset('assets/img/logo.png');
    $srcKiri = $isPdfMode ? $logoKiri : asset('assets/img/dikdasmen.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Rekap Nilai - {{ $kelas->nama_kelas }} - {{ $periode->nama_periode }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm 8mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 8pt; color: #111; margin: 0; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none; vertical-align: middle; }
        .kop-logo { width: 100px; text-align: center; }
        .kop-tengah { text-align: center; }
        .garis-tebal { border-top: 3px solid #000; margin-top: 4px; }
        .garis-tipis { border-top: 1px solid #000; margin-top: 1px; margin-bottom: 8px; }
        h1 { font-size: 13pt; margin: 0 0 2px 0; text-align: center; }
        .meta { text-align: center; font-size: 9pt; margin-bottom: 10px; }
        table.nilai { width: 100%; border-collapse: collapse; table-layout: fixed; page-break-inside: auto; }
        table.nilai thead { display: table-header-group; }
        table.nilai tr { page-break-inside: avoid; }
        table.nilai th, table.nilai td { border: 1px solid #444; padding: 3px 2px; overflow: hidden; }
        table.nilai th { background: #e5e7eb; font-size: 7pt; word-break: break-word; }
        table.nilai td { font-size: 7.5pt; word-break: break-word; }
        td.num { text-align: center; }
        td.nama { text-align: left; }
        tfoot td { font-weight: bold; background: #f3f4f6; }
        .toolbar { margin: 12px; text-align: right; }
        .toolbar button { padding: 8px 18px; font-size: 13px; font-weight: bold; border: none; border-radius: 8px; background: #0284c7; color: #fff; cursor: pointer; }
        .ket { margin-top: 8px; font-size: 8pt; color: #555; }
        @media print {
            .toolbar { display: none; }
        }
    </style>
</head>
<body>
    @if(! $isPdfMode)
        <div class="toolbar">
            <button onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    @endif

    <div style="text-align: center;">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if(file_exists($logoKiri))
                        <img src="{{ $srcKiri }}" width="110">
                    @endif
                </td>
                <td class="kop-tengah">
                    <div style="font-size: 10pt; font-weight: bold; color: #007bff;">
                        MAJELIS PENDIDIKAN DASAR DAN MENENGAH PENDIDIKAN NONFORMAL
                    </div>
                    <div style="font-size: 10pt; font-weight: bold; color: #007bff;">
                        PIMPINAN WILAYAH MUHAMMADIYAH JAWA BARAT
                    </div>
                    <div style="font-size: 18pt; font-weight: bold; color: #007bff;">
                        SMK MUHAMMADIYAH KANDANGHAUR
                    </div>
                    <div style="font-size: 12pt; font-weight: bold;">
                        Terakreditasi &ldquo;A&rdquo; (Unggul)
                    </div>
                    <div style="font-size: 12pt; font-weight: bold;">
                        Nomor : 18572022/BAN-SM/SK/2022
                    </div>
                    <div style="font-size: 9pt; margin-top: 5px;">
                        Konsentrasi Keahlian : Teknik Elektronika Industri, Teknik
                        Pengelasan, Teknik Kendaraan Ringan, <br>
                        Teknik Komputer dan Jaringan, Teknik Sepeda Motor, Layanan
                        Penunjang Kefarmasian Klinis dan Komunitas
                    </div>
                    <div style="font-size: 6pt;">
                        Jl. Raya Karanganyar No. 28/A Kec. Kandanghaur Kab. Indramayu
                        45254 Telp. (0234) 507239
                        email : smkmuhkdh@gmail.com website :
                        https://www.smkmuhkandanghaur.sch.id
                    </div>
                </td>
                <td class="kop-logo">
                    @if(file_exists($logoKanan))
                        <img src="{{ $srcKanan }}" width="80">
                    @endif
                </td>
            </tr>
        </table>

        <div class="garis-tebal"></div>
        <div class="garis-tipis"></div>
    </div>

    <h1>REKAP NILAI HASIL UJIAN</h1>
    <div class="meta">
        {{ $periode->nama_periode }} &bull; Kelas {{ $kelas->nama_kelas }}
        &bull; {{ count($rows) }} siswa &bull; {{ count($mapels) }} mapel
        &bull; Dicetak {{ date('d-m-Y H:i') }}
    </div>

    <table class="nilai">
        <colgroup>
            <col style="width:26px;">
            <col style="width:52px;">
            <col style="width:66px;">
            <col>
            @foreach($mapels as $mapel)
                <col>
            @endforeach
            <col style="width:36px;">
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                @foreach($mapels as $mapel)
                    <th>{{ $mapel->nama_mapel }}</th>
                @endforeach
                <th>Rata2</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="num">{{ $row['no'] }}</td>
                    <td class="num">{{ $row['nis'] }}</td>
                    <td class="num">{{ $row['nisn'] }}</td>
                    <td class="nama">{{ $row['nama'] }}</td>
                    @foreach($mapels as $mapel)
                        <td class="num">{{ number_format($row['nilai'][$mapel->id] ?? 0, 1) }}</td>
                    @endforeach
                    <td class="num"><strong>{{ number_format($row['rata'], 1) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + count($mapels) }}" style="text-align:center;">Tidak ada siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ket">Keterangan: nilai 0 berarti siswa tidak/belum mengerjakan mapel tersebut. Rata2 = rata-rata seluruh kolom mapel.</div>
</body>
</html>
