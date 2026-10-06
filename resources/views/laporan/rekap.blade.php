@php
    // Pratinjau browser pakai URL publik.
    $srcKanan = asset('assets/img/logo.png');
    $srcKiri = asset('assets/img/dikdasmen.png');
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
        table.nilai th, table.nilai td { border: 1px solid #444; padding: 3px 4px; overflow: hidden; }
        table.nilai th { background: #2196f3; color: #fff; font-size: 7.5pt; word-break: break-word; vertical-align: middle; text-align: center; }
        th.mapel-vert { vertical-align: bottom; padding: 6px 1px; }
        .vwrap { display: inline-flex; gap: 3px; }
        .vcol { writing-mode: vertical-rl; transform: rotate(180deg); white-space: nowrap; }
        table.nilai td { font-size: 8pt; word-break: break-word; }
        td.num { text-align: center; }
        td.nama { text-align: left; }
        .toolbar { margin: 12px; text-align: right; }
        .toolbar button { padding: 8px 18px; font-size: 13px; font-weight: bold; border: none; border-radius: 8px; background: #0284c7; color: #fff; cursor: pointer; }
        .ket { margin-top: 8px; font-size: 8pt; color: #555; }
        @media print {
            .toolbar { display: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

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
            <col style="width:70px;">
            <col style="width:150px;">
            @foreach($mapels as $mapel)
                <col style="width:30px;">
            @endforeach
            <col style="width:44px;">
            <col style="width:40px;">
            <col style="width:44px;">
            <col style="width:44px;">
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                @foreach($mapels as $mapel)
                    <th class="mapel-vert"><span class="vwrap">@foreach($mapel->nama_baris as $baris)<span class="vcol">{!! $baris !!}</span>@endforeach</span></th>
                @endforeach
                <th class="mapel-vert"><span class="vwrap"><span class="vcol">Kumulatif</span></span></th>
                <th class="mapel-vert"><span class="vwrap"><span class="vcol">Nilai</span></span><span class="vwrap"><span class="vcol">Rata-Rata</span></span></th>
                <th class="mapel-vert"><span class="vwrap"><span class="vcol">Predikat</span></span></th>
                <th class="mapel-vert"><span class="vwrap"><span class="vcol">Peringkat</span></span></th>
            </tr>
        </thead>
        <tbody>
            @php $peringkatMax = $rows->max('peringkat'); @endphp
            @forelse($rows as $row)
                <tr @if(($row['peringkat'] ?? 0) === 1) style="background:#bbf7d0;" @elseif(! empty($row['peringkat']) && $row['peringkat'] === $peringkatMax && $peringkatMax > 1) style="background:#fecaca;" @endif>
                    <td class="num">{{ $row['no'] }}</td>
                    <td class="num">{{ $row['nisn'] }}</td>
                    <td class="nama">{{ $row['nama'] }}</td>
                    @foreach($mapels as $mapel)
                        @php $skorMapel = (int) round($row['nilai'][$mapel->id] ?? 0); @endphp
                        <td class="num" @if($skorMapel < 75) style="color:#dc2626; font-weight:bold;" @endif>{{ $skorMapel }}</td>
                    @endforeach
                    <td class="num"><strong>{{ (int) round($row['kumulatif']) }}</strong></td>
                    <td class="num" @if($row['rata'] < 75) style="color:#dc2626;" @endif><strong>{{ rtrim(rtrim(number_format($row['rata'], 1), '0'), '.') }}</strong></td>
                    <td class="num">{{ $row['predikat'] }}</td>
                    <td class="num"><strong>{{ $row['peringkat'] ?? '-' }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 7 + count($mapels) }}" style="text-align:center;">Tidak ada siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ket">Keterangan: nilai 0 berarti siswa tidak/belum mengerjakan mapel tersebut.</div>

    @php
        $bulanId = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $tanggalTtd = date('d') . ' ' . $bulanId[(int) date('n')] . ' ' . date('Y');
        $ttdKepsek = $setting->ttd_kepala_sekolah ?? null;
        $srcTtd = $ttdKepsek ? \Illuminate\Support\Facades\Storage::disk('r2')->url($ttdKepsek) : null;
    @endphp

    <table style="width:100%; border-collapse:collapse; margin-top:24px; font-size:8pt;">
        <tr>
            <td style="width:35%; border:none;" valign="top">
                <div style="display:inline-block; text-align:center;">
                    Mengetahui,<br>
                    Kepala Sekolah,<br>
                    <div style="height:70px;">
                        @if(! empty($srcTtd))
                            <img src="{{ $srcTtd }}" style="max-width:150px; max-height:70px;">
                        @endif
                    </div>
                    @if(! empty($setting->nama_kepala_sekolah))
                        <strong><u>{{ $setting->nama_kepala_sekolah }}</u></strong>
                    @else
                        <span style="display:inline-block; width:95px; border-bottom:1px dotted #000;">&nbsp;</span>
                    @endif
                </div>
            </td>
            <td style="border:none;"></td>
            <td style="width:35%; border:none; text-align:right;" valign="top">
                <div style="display:inline-block; text-align:center;">
                    Kandanghaur, {{ $tanggalTtd }}<br>
                    Wali Kelas<br>
                    <div style="height:70px;"></div>
                    @if(! empty($namaWaliKelas))
                        <strong><u>{{ $namaWaliKelas }}</u></strong>
                    @else
                        <span style="display:inline-block; width:95px; border-bottom:1px dotted #000;">&nbsp;</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
