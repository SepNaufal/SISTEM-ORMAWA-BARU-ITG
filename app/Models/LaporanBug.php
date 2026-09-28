<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanBug extends Model
{
    use HasFactory;

    protected $table = 'laporan_bugs';

    protected $fillable = [
        'kode_laporan',
        'user_id',
        'nama_pelapor',
        'email_pelapor',
        'no_hp_pelapor',
        'role_pelapor',
        'prodi_pelapor',
        'halaman_url',
        'judul',
        'tingkat_urgensi',
        'kategori',
        'deskripsi',
        'tangkapan_layar',
        'status',
        'catatan_bkhm',
        'tanggapan_it',
        'diteruskan_ke_it_at',
        'diselesaikan_at',
    ];

    protected $casts = [
        'diteruskan_ke_it_at' => 'datetime',
        'diselesaikan_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate Kode Laporan unik: BUG-YYYY-XXXXXX
     */
    public static function generateKodeLaporan(): string
    {
        $year = date('Y');
        $prefix = "BUG-{$year}-";
        $charset = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $randomSuffix = '';
            for ($i = 0; $i < 6; $i++) {
                $randomSuffix .= $charset[random_int(0, strlen($charset) - 1)];
            }
            $kode = $prefix . $randomSuffix;
        } while (self::where('kode_laporan', $kode)->exists());

        return $kode;
    }

    /**
     * Label Status Ramah Pengguna
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bkhm' => 'Menunggu Triage BKHM',
            'diteruskan_ke_it' => 'Diteruskan ke Tim IT',
            'sedang_diperbaiki' => 'Sedang Diperbaiki Tim IT',
            'selesai' => 'Selesai / Teratasi',
            'ditolak' => 'Ditolak / Tidak Valid',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Warna Status Tailwind (Semantik R-29 DESIGN.md)
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bkhm' => 'bg-amber-50 text-amber-900 border-amber-300',
            'diteruskan_ke_it' => 'bg-indigo-50 text-indigo-900 border-indigo-300',
            'sedang_diperbaiki' => 'bg-purple-50 text-purple-900 border-purple-300',
            'selesai' => 'bg-emerald-50 text-emerald-900 border-emerald-300',
            'ditolak' => 'bg-rose-50 text-rose-900 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    /**
     * Warna Tingkat Urgensi
     */
    public function getUrgensiBadgeAttribute(): string
    {
        return match ($this->tingkat_urgensi) {
            'kritis' => 'bg-rose-100 text-rose-800 font-bold border border-rose-300',
            'tinggi' => 'bg-amber-100 text-amber-800 font-semibold border border-amber-300',
            'sedang' => 'bg-indigo-50 text-indigo-700 font-medium border border-indigo-200',
            'rendah' => 'bg-slate-100 text-slate-700 font-normal border border-slate-200',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    /**
     * Label Kategori
     */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'error_sistem' => 'Error Sistem / Pesan 500',
            'tampilan_uiux' => 'Tampilan / UI Ganjil',
            'fitur_gagal' => 'Fitur Macet / Gagal Simpan',
            'usulan' => 'Usulan Pengembangan Fitur',
            default => ucfirst(str_replace('_', ' ', $this->kategori)),
        };
    }
}
