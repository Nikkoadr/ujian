@php
    $bulanId = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    $tanggalTtd = date('d') . ' ' . $bulanId[(int) date('n')] . ' ' . date('Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rapor Peserta Didik</title>

    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #e5e5e5;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 11px;
        }


        /* =====================================================
           TOOLBAR (tidak ikut tercetak)
        ===================================================== */

        .toolbar {
            max-width: 210mm;
            margin: 20px auto 0 auto;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .toolbar button,
        .toolbar a {
            padding: 8px 18px;
            font-size: 13px;
            font-weight: bold;
            font-family: inherit;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .toolbar .btn-cetak {
            background: #0284c7;
            color: #fff;
        }

        .toolbar .btn-kembali {
            background: #fff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }


        /* =====================================================
           HALAMAN A4
        ===================================================== */

        .page {
            position: relative;

            width: 210mm;
            min-height: 297mm;

            margin: 20px auto;

            padding: 12mm 10mm;

            background: #fff;

            box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);

            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }


        /* =====================================================
           IDENTITAS PESERTA DIDIK
        ===================================================== */

        .identitas {
            width: 100%;
            border-collapse: collapse;

            margin-bottom: 8px;
        }

        .identitas td {
            padding: 2px 0;
            vertical-align: top;
            line-height: 1.4;
        }

        .identitas .label {
            width: 85px;
            font-weight: bold;
            white-space: nowrap;
        }

        .identitas .separator {
            width: 10px;
            text-align: center;
        }

        .identitas .value {
            width: 205px;
        }

        .identitas .label-right {
            width: 75px;
            font-weight: bold;
            white-space: nowrap;
        }


        /* =====================================================
           JUDUL
        ===================================================== */

        .judul {
            margin-top: 8px;
            margin-bottom: 5px;
            font-weight: bold;
        }


        /* =====================================================
           TABEL NILAI
        ===================================================== */

        .nilai {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .nilai th,
        .nilai td {
            border: 1px solid #000;
        }

        .nilai th {
            height: 39px;
            padding: 4px;
            background: #08a9dc;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .nilai td {
            height: 22px;
            padding: 3px 6px;
            vertical-align: middle;
        }


        /* =====================================================
           LEBAR KOLOM
        ===================================================== */

        .nilai .col-no { width: 35px; }
        .nilai .col-mapel { width: 48%; }
        .nilai .col-nilai { width: 45px; }
        .nilai .col-predikat { width: 40%; }


        /* =====================================================
           ALIGNMENT
        ===================================================== */

        .nilai td:first-child { text-align: center; }
        .nilai .angka { text-align: center; }
        .nilai .predikat { text-align: center; }


        /* =====================================================
           KELOMPOK MATA PELAJARAN
        ===================================================== */

        .nilai .kelompok td {
            height: 21px;
            padding: 3px 5px;
            background: #d9d9d9;
            text-align: left;
            font-weight: bold;
        }


        /* =====================================================
           PEROLEHAN AKADEMIK
        ===================================================== */

        .akademik {
            margin-top: 15px;
        }

        .akademik-title {
            margin-bottom: 4px;
            font-weight: bold;
        }

        .pencapaian {
            width: 245px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .pencapaian th,
        .pencapaian td {
            height: 21px;
            padding: 4px 6px;
            border: 1px solid #000;
        }

        .pencapaian th {
            background: #08a9dc;
            text-align: center;
            font-weight: bold;
        }

        .pencapaian th:first-child { width: 58%; }
        .pencapaian th:last-child  { width: 42%; }

        .pencapaian td:last-child {
            text-align: center;
        }


        /* =====================================================
           TANDA TANGAN ORANG TUA & WALI KELAS
        ===================================================== */

        .ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 37px;
        }

        .ttd td {
            width: 50%;
            vertical-align: top;
        }

        .ttd .kanan {
            text-align: right;
        }

        .ttd .kanan .isi-kanan {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }

        .ruang-ttd {
            height: 65px;
        }

        .garis {
            display: inline-block;
            width: 95px;
            height: 1px;
            border-bottom: 1px dotted #000;
        }


        /* =====================================================
           KEPALA SEKOLAH
           DI-TENGAH ANTARA ORANG TUA & WALI KELAS
        ===================================================== */

        .ttd-kepala {
            width: 100%;
            margin-top: 12px;

            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .kepala {
            width: 200px;
            text-align: center;
        }

        .kepala-jabatan {
            margin-bottom: 2px;
        }

        .signature {
            width: 150px;
            height: 70px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature img {
            max-width: 150px;
            max-height: 70px;
            object-fit: contain;
        }

        .nama-kepala {
            margin-top: 2px;
            text-align: center;
        }


        /* =====================================================
           PRINT
        ===================================================== */

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                min-height: 297mm;
                background: #fff;
            }

            body {
                font-size: 11px;
            }

            .toolbar {
                display: none;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                padding: 12mm 10mm;
                box-shadow: none;
            }
        }

    </style>
</head>


<body>

<div class="toolbar" id="toolbarCetak">
    <a class="btn-kembali" href="{{ route('laporan.individu', ['periode_ujian_id' => $periode->id, 'kelas_id' => $kelas->id]) }}">Kembali</a>
    <button class="btn-cetak" onclick="window.print()">Cetak</button>
</div>

<script>
    // Jika dimuat di dalam iframe (cetak dari halaman daftar), toolbar tidak perlu.
    if (window.self !== window.top) {
        document.getElementById('toolbarCetak').style.display = 'none';
    }
</script>

@foreach($rapors as $rapor)
<div class="page">

    <!-- =====================================================
         KEPALA LAPORAN
    ====================================================== -->

    <div style="text-align:center; margin-bottom:10px; font-size:16px; font-weight:bold;">
        Hasil {{ $periode->nama_periode }}
    </div>

    <!-- =====================================================
         IDENTITAS PESERTA DIDIK
    ====================================================== -->

    <table class="identitas">
        <tr>
            <td class="label">Nama Peserta Didik</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ strtoupper($rapor['nama']) }}</strong></td>
            <td class="label-right">Nama Sekolah</td>
            <td class="separator">:</td>
            <td>{{ $setting->nama_sekolah ?? 'SMK Muhammadiyah Kandanghaur' }}</td>
        </tr>

        <tr>
            <td class="label">NIS / NISN</td>
            <td class="separator">:</td>
            <td class="value">{{ $rapor['nis'] }} / {{ $rapor['nisn'] }}</td>
            <td class="label-right">Alamat Sekolah</td>
            <td class="separator">:</td>
            <td>{{ $setting->alamat_sekolah ?? 'Jl. Karanganyar No.28/A, Kec.Kandanghaur' }}</td>
        </tr>

        <tr>
            <td class="label">Tingkat</td>
            <td class="separator">:</td>
            <td class="value">{{ $identitas['tingkat'] }}</td>
            <td class="label-right">Kelas</td>
            <td class="separator">:</td>
            <td>{{ $kelas->nama_kelas }}</td>
        </tr>

        <tr>
            <td class="label">Tahun Pelajaran</td>
            <td class="separator">:</td>
            <td class="value">{{ $identitas['tahun'] }}</td>
            <td class="label-right">Semester</td>
            <td class="separator">:</td>
            <td>{{ $identitas['semester'] }}</td>
        </tr>
    </table>


    <!-- =====================================================
         A. NILAI AKADEMIK
    ====================================================== -->

    <div class="judul">A.Nilai Akademik</div>

    <table class="nilai">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-mapel">Mata Pelajaran</th>
                <th class="col-nilai">Nilai<br>Akhir</th>
                <th class="col-predikat">Predikat</th>
            </tr>
        </thead>

        <tbody>

            @foreach($rapor['daftarMapel'] as $mapel)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mapel['nama'] }}</td>
                <td class="angka">{{ $mapel['nilai'] }}</td>
                <td class="predikat"><i>{{ $mapel['predikat'] }}</i></td>
            </tr>
            @endforeach

        </tbody>
    </table>


    <!-- =====================================================
         B. PEROLEHAN AKADEMIK
    ====================================================== -->

    <div class="akademik">

        <div class="akademik-title">B. Perolehan Akademik</div>

        <table class="pencapaian">
            <thead>
                <tr>
                    <th>Catatan Perolehan</th>
                    <th>Skor Penilaian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.&nbsp;&nbsp; Kumulatif</td>
                    <td>{{ $rapor['kumulatif'] }}</td>
                </tr>
                <tr>
                    <td>2.&nbsp;&nbsp; Rata-Rata</td>
                    <td>{{ $rapor['rata'] }}</td>
                </tr>
                <tr>
                    <td>3.&nbsp;&nbsp; Predikat</td>
                    <td>{{ $rapor['predikat'] }}</td>
                </tr>
                <tr>
                    <td>4.&nbsp;&nbsp; Peringkat</td>
                    <td>{{ $rapor['peringkat'] ?? '' }}</td>
                </tr>
            </tbody>
        </table>

    </div>


    <!-- =====================================================
         TANDA TANGAN ORANG TUA & WALI KELAS
    ====================================================== -->

    <table class="ttd">
        <tr>
            <td>Mengetahui:</td>
            <td class="kanan"><div class="isi-kanan">Kandanghaur, {{ $tanggalTtd }}</div></td>
        </tr>

        <tr>
            <td>Orang Tua / Wali,</td>
            <td class="kanan"><div class="isi-kanan">Wali Kelas</div></td>
        </tr>

        <tr>
            <td>
                <div class="ruang-ttd"></div>
                <span class="garis"></span>
            </td>

            <td class="kanan">
                <div class="ruang-ttd"></div>
                <div class="isi-kanan">
                    @if(! empty($namaWaliKelas))
                        <strong><u>{{ $namaWaliKelas }}</u></strong>
                    @else
                        <span class="garis"></span>
                    @endif
                </div>
            </td>
        </tr>
    </table>


    <!-- =====================================================
         KEPALA SEKOLAH
         (Di tengah antara orang tua & wali kelas)
    ====================================================== -->

    <div class="ttd-kepala">
        <div class="kepala">

            <div class="kepala-jabatan">Mengetahui,</div>

            <div>Kepala Sekolah</div>

            <div class="signature">
                @if(! empty($setting->ttd_kepala_sekolah))
                    <img src="{{ Storage::disk('r2')->url($setting->ttd_kepala_sekolah) }}" alt="TTD Kepala Sekolah">
                @endif
            </div>

            <div class="nama-kepala">
                @if(! empty($setting->nama_kepala_sekolah))
                    <strong><u>{{ $setting->nama_kepala_sekolah }}</u></strong>
                @else
                    <span class="garis"></span>
                @endif
            </div>

        </div>
    </div>

</div>
@endforeach

</body>
</html>
