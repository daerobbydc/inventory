<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case AdminFakultas = 'admin_fakultas';
    case PicRuangan = 'pic_ruangan';
    case Teknisi = 'teknisi';
    case Dosen = 'dosen';
    case Tendik = 'tendik';
    case Mahasiswa = 'mahasiswa';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin (Sarpras)',
            self::AdminFakultas => 'Admin Fakultas',
            self::PicRuangan => 'PIC Ruangan / Laboran',
            self::Teknisi => 'Teknisi / Pemeliharaan',
            self::Dosen => 'Dosen',
            self::Tendik => 'Tenaga Kependidikan',
            self::Mahasiswa => 'Mahasiswa',
        };
    }
}
