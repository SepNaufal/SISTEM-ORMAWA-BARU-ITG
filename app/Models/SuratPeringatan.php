<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SuratPeringatan extends Model
{
    protected $fillable = [
        'tipe_sasaran',
        'target_user_id',
        'target_nim',
        'target_nama',
        'target_prodi',
        'target_kontak',
        'target_mahasiswas',
        'nomor_surat',
        'tingkat',
        'status',
        'perihal',
        'alasan_singkat',
        'deskripsi',
        'sanksi',
        'catatan_wr3',
        'catatan_bkhm',
        'is_internal_bpm',
        'validated_by',
        'validated_at',
        'tanggal_surat',
        'penandatangan',
        'pejabat_nama',
        'pejabat_nidn',
        'pejabat_jabatan',
        'pdf_path',
        'created_by'
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'validated_at'  => 'datetime',
        'is_internal_bpm' => 'boolean',
        'target_mahasiswas' => 'array',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function tandaTanganDigitals(): MorphMany
    {
        return $this->morphMany(TandaTanganDigital::class, 'signable')->latest();
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isMenungguValidasi(): bool
    {
        return $this->status === 'menunggu_validasi';
    }

    public function isMenungguBkhm(): bool
    {
        return $this->status === 'menunggu_bkhm';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeMenungguValidasi($query)
    {
        return $query->where('status', 'menunggu_validasi');
    }

    public function scopeMenungguBkhm($query)
    {
        return $query->where('status', 'menunggu_bkhm');
    }

    /**
     * Penerbit SP diturunkan dari role pembuat: BPM atau BKHM.
     */
    public function getPenerbitLabelAttribute(): string
    {
        if ($this->is_internal_bpm) {
            return 'BPM';
        }

        return $this->creator?->hasRole('bpm') ? 'BPM' : 'BKHM';
    }

    public function isMahasiswa(): bool
    {
        return $this->tipe_sasaran === 'mahasiswa';
    }

    public function isOrmawa(): bool
    {
        return $this->tipe_sasaran !== 'mahasiswa';
    }

    /**
     * Daftar lengkap penerima mahasiswa. Memakai kolom JSON target_mahasiswas
     * bila ada, dan jatuh kembali ke kolom skalar untuk data lama.
     */
    public function getPenerimaMahasiswaAttribute(): array
    {
        $list = $this->target_mahasiswas ?? [];

        if (empty($list)) {
            if ($this->target_nim || $this->target_nama) {
                $list = [[
                    'nim' => $this->target_nim,
                    'nama' => $this->target_nama,
                    'prodi' => $this->target_prodi,
                    'kontak' => $this->target_kontak,
                ]];
            }
        }

        return array_map(function ($m) {
            return [
                'nim' => $m['nim'] ?? null,
                'nama' => $m['nama'] ?? null,
                'prodi' => $m['prodi'] ?? null,
                'kontak' => $m['kontak'] ?? null,
            ];
        }, $list);
    }

    public function getNamaPenerimaAttribute(): string
    {
        if ($this->isMahasiswa()) {
            $penerima = $this->penerima_mahasiswa;
            $first = $penerima[0]['nama'] ?? null;
            if (! $first) {
                return 'Mahasiswa ITG';
            }
            $lain = count($penerima) - 1;
            return $lain > 0 ? $first . ' dan ' . $lain . ' mahasiswa lain' : $first;
        }
        return $this->target?->name ?? 'Organisasi Mahasiswa';
    }

    public function getIdentitasPenerimaAttribute(): string
    {
        if ($this->isMahasiswa()) {
            $penerima = $this->penerima_mahasiswa;
            $first = $penerima[0] ?? null;
            if (! $first) {
                return 'Mahasiswa ITG';
            }
            $prodi = $first['prodi'] ? " ({$first['prodi']})" : '';
            $identitas = "NIM. " . ($first['nim'] ?: '-') . $prodi;
            $lain = count($penerima) - 1;
            return $lain > 0 ? $identitas . ' dan ' . $lain . ' mahasiswa lain' : $identitas;
        }
        return 'Organisasi Kemahasiswaan ITG';
    }
}
