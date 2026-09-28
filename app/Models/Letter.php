<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Letter extends Model
{
    protected $fillable = [
        'user_id',
        'proposal_otomatis_id',
        'type',
        'nomor_surat',
        'perihal',
        'content',
        'metadata',
    ];

    protected $casts = ['metadata' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(ProposalOtomatis::class, 'proposal_otomatis_id');
    }

    public function tandaTanganDigitals(): MorphMany
    {
        return $this->morphMany(TandaTanganDigital::class, 'signable')->orderBy('signer_index');
    }

    public function tandaTanganUtama(): ?TandaTanganDigital
    {
        return $this->tandaTanganDigitals()->first();
    }

    /**
     * Daftar penandatangan yang sudah dinormalkan. Entri lama {role, nama}
     * diperlakukan sebagai penandatangan internal agar surat lama tetap tampil.
     */
    public function getPenandatanganListAttribute(): array
    {
        $meta = $this->metadata ?? [];
        $list = $meta['penandatangan_list'] ?? [];

        if (empty($list) && ! empty($meta['penandatangan'])) {
            $owner = $this->user ?? Auth::user();
            $role = $meta['penandatangan'];
            $nama = match ($role) {
                'sekretaris' => $owner->nama_sekretaris ?? $owner->name ?? null,
                'bendahara' => $owner->nama_bendahara ?? $owner->name ?? null,
                default => $owner->nama_ketua ?? $owner->name ?? null,
            };
            $list = [['role' => $role, 'nama' => $nama]];
        }

        return array_map(function ($p) {
            $role = $p['role'] ?? null;
            return [
                'jenis' => $p['jenis'] ?? 'internal',
                'role' => $role,
                'nama' => $p['nama'] ?? '-',
                'jabatan' => $p['jabatan'] ?? ($role ? ucfirst($role) : 'Penandatangan'),
            ];
        }, $list);
    }

    /**
     * Peta tanda tangan berdasarkan signer_index, untuk dicocokkan dengan posisi penandatangan.
     */
    public function getSignaturesByIndexAttribute(): array
    {
        return $this->tandaTanganDigitals->keyBy('signer_index')->all();
    }
}
