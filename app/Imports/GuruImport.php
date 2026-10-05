<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    private int $baris = 1;

    public function model(array $row)
    {
        // +1 untuk baris judul di Excel.
        $this->baris++;
        $no = $this->baris;

        $nip = trim((string) ($row['nip'] ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));
        $password = (string) ($row['password'] ?? '');
        $jkInput = strtoupper(trim((string) ($row['jenis_kelamin'] ?? '')));

        if ($nip === '') {
            throw new \Exception("Baris {$no}: kolom nip kosong.");
        }

        if ($nama === '') {
            throw new \Exception("Baris {$no}: kolom nama kosong.");
        }

        if ($email === '') {
            throw new \Exception("Baris {$no}: kolom email kosong.");
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Baris {$no}: email '{$email}' tidak valid.");
        }

        if ($password === '') {
            throw new \Exception("Baris {$no}: kolom password kosong.");
        }

        if (! in_array($jkInput, ['L', 'P'], true)) {
            throw new \Exception("Baris {$no}: jenis_kelamin harus diisi L atau P (bukan '{$row['jenis_kelamin']}').");
        }

        if (User::where('email', $email)->exists()) {
            throw new \Exception("Baris {$no}: email '{$email}' sudah terdaftar.");
        }

        if (Guru::where('nip', $nip)->exists()) {
            throw new \Exception("Baris {$no}: NIP '{$nip}' sudah terdaftar.");
        }

        return DB::transaction(function () use ($row, $nip, $nama, $email, $password, $jkInput) {
            $user = User::create([
                'nama' => $nama,
                'jenis_kelamin' => $jkInput === 'L' ? 'laki-laki' : 'perempuan',
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => '2',
                'status' => 'aktif',
            ]);

            return new Guru([
                'user_id' => $user->id,
                'nip' => $nip,
                'gelar_depan' => trim((string) ($row['gelar_depan'] ?? '')) ?: null,
                'gelar_belakang' => trim((string) ($row['gelar_belakang'] ?? '')) ?: null,
            ]);
        });
    }
}
