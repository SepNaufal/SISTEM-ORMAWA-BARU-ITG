<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Generator Surat Otomatis') }}</h2></x-slot>
    <div class="py-12" x-data="{ type: '{{ old('type','undangan') }}' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('generator.letters.store') }}" method="POST">
                    @csrf
                    <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Informasi Dasar Surat</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <x-input-label for="type" :value="__('Jenis Surat')" />
                            <select id="type" name="type" x-model="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="undangan">Surat Undangan</option>
                                <option value="tugas">Surat Tugas / Mandat</option>
                                <option value="permohonan">Surat Permohonan (Alat/Tempat)</option>
                                <option value="keterangan_aktif">Surat Keterangan Aktif</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="nomor_surat" :value="__('Nomor Surat (Opsional)')" />
                            <x-text-input id="nomor_surat" name="nomor_surat" type="text" class="mt-1 block w-full" placeholder="Contoh: 001/HIMATIF/ITG/V/2024" :value="old('nomor_surat')" />
                        </div>
                        <div>
                            <x-input-label for="perihal" :value="__('Perihal')" />
                            <x-text-input id="perihal" name="perihal" type="text" class="mt-1 block w-full" required placeholder="Contoh: Undangan Pemateri Seminar Nasional" :value="old('perihal')" />
                            <x-input-error :messages="$errors->get('perihal')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="tujuan" :value="__('Tujuan Surat')" />
                            <x-text-input id="tujuan" name="tujuan" type="text" class="mt-1 block w-full" required placeholder="Contoh: Yth. Bapak Ir. Budi Santoso, M.T." :value="old('tujuan')" />
                            <x-input-error :messages="$errors->get('tujuan')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Detail Undangan -->
                    <div x-show="type==='undangan'" x-cloak class="mt-6">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Undangan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div><x-input-label for="kalimat_pembuka" :value="__('Kalimat Pembuka')" /><textarea id="kalimat_pembuka" name="kalimat_pembuka" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Sehubungan dengan akan dilaksanakannya kegiatan Seminar Nasional, kami bermaksud mengundang Bapak/Ibu untuk hadir sebagai pemateri.">{{ old('kalimat_pembuka') }}</textarea></div>
                            <div><x-input-label for="nama_acara" :value="__('Nama Acara')" /><x-text-input id="nama_acara" name="nama_acara" type="text" class="mt-1 block w-full" :value="old('nama_acara')" /></div>
                            <div><x-input-label for="hari_tanggal" :value="__('Hari / Tanggal')" /><x-text-input id="hari_tanggal" name="hari_tanggal" type="text" class="mt-1 block w-full" placeholder="Senin, 20 Mei 2024" :value="old('hari_tanggal')" /></div>
                            <div><x-input-label for="waktu" :value="__('Waktu')" /><x-text-input id="waktu" name="waktu" type="text" class="mt-1 block w-full" placeholder="08.00 s.d Selesai" :value="old('waktu')" /></div>
                            <div><x-input-label for="tempat" :value="__('Tempat')" /><x-text-input id="tempat" name="tempat" type="text" class="mt-1 block w-full" placeholder="Aula ITG" :value="old('tempat')" /></div>
                        </div>
                    </div>

                    <!-- Detail Tugas -->
                    <div x-show="type==='tugas'" x-cloak class="mt-6">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Surat Tugas</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div><x-input-label for="nama_petugas" :value="__('Nama Petugas')" /><x-text-input id="nama_petugas" name="nama_petugas" type="text" class="mt-1 block w-full" :value="old('nama_petugas')" /></div>
                            <div><x-input-label for="nim_tugas" :value="__('NIM')" /><x-text-input id="nim_tugas" name="nim" type="text" class="mt-1 block w-full" x-bind:disabled="type !== 'tugas'" :value="old('nim')" /></div>
                            <div><x-input-label for="uraian_tugas" :value="__('Uraian Tugas')" /><textarea id="uraian_tugas" name="uraian_tugas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Menjadi delegasi dalam kegiatan Musyawarah Nasional...">{{ old('uraian_tugas') }}</textarea></div>
                            <div><x-input-label for="tanggal_pelaksanaan" :value="__('Tanggal Pelaksanaan')" /><x-text-input id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" type="text" class="mt-1 block w-full" placeholder="20 - 25 Mei 2024" :value="old('tanggal_pelaksanaan')" /></div>
                        </div>
                    </div>

                    <!-- Detail Permohonan -->
                    <div x-show="type==='permohonan'" x-cloak class="mt-6">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Permohonan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div><x-input-label for="nama_alat_tempat" :value="__('Nama Alat / Tempat yang Dipinjam')" /><x-text-input id="nama_alat_tempat" name="nama_alat_tempat" type="text" class="mt-1 block w-full" :value="old('nama_alat_tempat')" /></div>
                            <div><x-input-label for="waktu_penggunaan" :value="__('Waktu Penggunaan')" /><x-text-input id="waktu_penggunaan" name="waktu_penggunaan" type="text" class="mt-1 block w-full" placeholder="Rabu, 22 Mei 2024 Jam 13.00" :value="old('waktu_penggunaan')" /></div>
                            <div><x-input-label for="alasan_tujuan" :value="__('Alasan / Tujuan Peminjaman')" /><textarea id="alasan_tujuan" name="alasan_tujuan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('alasan_tujuan') }}</textarea></div>
                        </div>
                    </div>

                    <!-- Detail Keterangan Aktif -->
                    <div x-show="type==='keterangan_aktif'" x-cloak class="mt-6">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Surat Keterangan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div><x-input-label for="nama_mahasiswa" :value="__('Nama Mahasiswa')" /><x-text-input id="nama_mahasiswa" name="nama_mahasiswa" type="text" class="mt-1 block w-full" :value="old('nama_mahasiswa')" /></div>
                            <div><x-input-label for="nim_ket" :value="__('NIM')" /><x-text-input id="nim_ket" name="nim" type="text" class="mt-1 block w-full" x-bind:disabled="type !== 'keterangan_aktif'" :value="old('nim')" /></div>
                            <div><x-input-label for="jabatan" :value="__('Jabatan di Organisasi')" /><x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" placeholder="Anggota Bidang Minat Bakat" :value="old('jabatan')" /></div>
                            <div><x-input-label for="keperluan" :value="__('Tujuan / Keperluan Surat')" /><textarea id="keperluan" name="keperluan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Persyaratan Beasiswa Unggulan">{{ old('keperluan') }}</textarea></div>
                        </div>
                    </div>

                    {{-- Penandatangan Dinamis --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200"
                         x-data="{
                            signers: [{ jenis: 'internal', role: 'ketua', nama: '{{ addslashes(Auth::user()->nama_ketua ?? Auth::user()->name) }}', jabatan: 'Ketua' }],
                            namaMap: {
                                ketua: '{{ addslashes(Auth::user()->nama_ketua ?? Auth::user()->name) }}',
                                sekretaris: '{{ addslashes(Auth::user()->nama_sekretaris ?? Auth::user()->name) }}',
                                bendahara: '{{ addslashes(Auth::user()->nama_bendahara ?? Auth::user()->name) }}'
                            },
                            roleLabels: { ketua: 'Ketua', sekretaris: 'Sekretaris', bendahara: 'Bendahara' },
                            updateNama(idx) {
                                let s = this.signers[idx];
                                if (s.jenis !== 'internal') return;
                                s.nama = this.namaMap[s.role] || '';
                                s.jabatan = this.roleLabels[s.role] || 'Penandatangan';
                            },
                            onJenisChange(idx) {
                                let s = this.signers[idx];
                                if (s.jenis === 'internal') {
                                    s.nama = this.namaMap[s.role] || '';
                                    s.jabatan = this.roleLabels[s.role] || 'Penandatangan';
                                } else {
                                    s.nama = '';
                                    s.jabatan = '';
                                }
                            },
                            addSigner() {
                                this.signers.push({ jenis: 'internal', role: 'sekretaris', nama: this.namaMap['sekretaris'] || '', jabatan: 'Sekretaris' });
                            },
                            removeSigner(idx) {
                                if (this.signers.length > 1) this.signers.splice(idx, 1);
                            }
                         }">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-indigo-600">Penandatangan Surat</h3>
                            <button type="button" @click="addSigner()"
                                class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 border border-indigo-300 rounded-lg px-3 py-1.5 hover:bg-indigo-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Penandatangan
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mb-3">Penandatangan internal memakai nama dari profil dan mendapat tanda tangan digital. Pilih "Pihak Luar" untuk nama di luar sistem.</p>

                        <template x-for="(signer, idx) in signers" :key="idx">
                            <div class="flex gap-3 items-start mb-3 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="flex-none">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold" x-text="idx + 1"></span>
                                </div>
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis</label>
                                        <select :name="'penandatangan_jenis[' + idx + ']'"
                                                x-model="signer.jenis"
                                                @change="onJenisChange(idx)"
                                                class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            <option value="internal">Internal (dari profil)</option>
                                            <option value="eksternal">Pihak Luar (manual)</option>
                                        </select>
                                    </div>
                                    <div x-show="signer.jenis === 'internal'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan</label>
                                        <select :name="'penandatangan_role[' + idx + ']'"
                                                x-model="signer.role"
                                                @change="updateNama(idx)"
                                                class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            <option value="ketua">Ketua</option>
                                            <option value="sekretaris">Sekretaris</option>
                                            <option value="bendahara">Bendahara</option>
                                        </select>
                                    </div>
                                    <div x-show="signer.jenis === 'eksternal'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Keterangan</label>
                                        <input type="text"
                                               :name="'penandatangan_jabatan[' + idx + ']'"
                                               x-model="signer.jabatan"
                                               placeholder="Contoh: Pembina UKM"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-sm" />
                                    </div>
                                    <div :class="signer.jenis === 'internal' ? '' : 'sm:col-span-2'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama</label>
                                        <input type="text"
                                               :name="'penandatangan_nama[' + idx + ']'"
                                               x-model="signer.nama"
                                               :placeholder="signer.jenis === 'internal' ? 'Dari profil' : 'Masukkan nama pihak luar...'"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-sm" />
                                    </div>
                                </div>
                                <div class="flex-none mt-5">
                                    <button type="button" @click="removeSigner(idx)"
                                            x-show="signers.length > 1"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 rounded transition-colors"
                                            title="Hapus penandatangan ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div x-show="signers.length === 1" class="w-7 h-7"></div>
                                </div>
                            </div>
                        </template>

                        <p class="text-xs text-slate-400 mt-1">Penandatangan internal mendapat tanda tangan digital yang dapat diverifikasi via QR code.</p>
                    </div>

                    <div class="flex justify-end mt-6"><x-primary-button>Generate Surat</x-primary-button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
