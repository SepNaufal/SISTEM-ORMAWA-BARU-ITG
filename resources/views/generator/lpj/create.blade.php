<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat LPJ Otomatis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <form action="{{ route('generator.lpj.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="proposal_id" value="{{ $proposal->id ?? '' }}">

                    <!-- BATANG TUBUH LPJ -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                            <h3 class="text-xl font-bold text-gray-800">Batang Tubuh LPJ</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                                <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" required placeholder="Contoh: Malam Keakraban HIMATIF 2024" value="{{ $proposal->nama_kegiatan ?? '' }}" />
                            </div>
                            <div>
                                <x-input-label for="pendahuluan" :value="__('Pendahuluan / Latar Belakang')" />
                                <textarea id="pendahuluan" name="pendahuluan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Jelaskan secara singkat mengenai terlaksananya kegiatan ini..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="waktu_tempat" :value="__('Waktu & Tempat Pelaksanaan')" />
                                <textarea id="waktu_tempat" name="waktu_tempat" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Sabtu, 15 Mei 2024 di Villa Garut. Dihadiri oleh 100 peserta."></textarea>
                            </div>
                            <div>
                                <x-input-label for="hasil_kegiatan" :value="__('Hasil Kegiatan')" />
                                <textarea id="hasil_kegiatan" name="hasil_kegiatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Apa saja capaian dari kegiatan ini?"></textarea>
                            </div>
                            <div>
                                <x-input-label for="hambatan" :value="__('Hambatan & Kendala')" />
                                <textarea id="hambatan" name="hambatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Sebutkan kendala teknis atau non-teknis yang dihadapi..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="saran" :value="__('Saran & Rekomendasi')" />
                                <textarea id="saran" name="saran" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Saran untuk panitia di masa mendatang..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="penutup" :value="__('Penutup')" />
                                <textarea id="penutup" name="penutup" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Demikian laporan pertanggungjawaban ini kami buat sebagai bahan evaluasi."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- LAPORAN REALISASI DANA -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                            <h3 class="text-xl font-bold text-gray-800">Laporan Realisasi Dana</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs font-medium text-gray-500 uppercase">
                                        <th class="px-4 py-3 text-left">Uraian</th>
                                        <th class="px-4 py-3 text-right w-40">Estimasi (Rp)</th>
                                        <th class="px-4 py-3 text-right w-40">Realisasi (Rp)</th>
                                        <th class="px-4 py-3 text-left">Keterangan</th>
                                        <th class="px-4 py-3 text-center w-16">#</th>
                                    </tr>
                                </thead>
                                <tbody id="realisasi-body">
                                    <tr class="border-b">
                                        <td class="p-2"><input type="text" name="realisasi_items[0][uraian]" class="w-full border-gray-300 rounded-sm" placeholder="Contoh: Konsumsi"></td>
                                        <td class="p-2"><input type="number" name="realisasi_items[0][estimasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                                        <td class="p-2"><input type="number" name="realisasi_items[0][realisasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                                        <td class="p-2"><input type="text" name="realisasi_items[0][keterangan]" class="w-full border-gray-300 rounded-sm"></td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="this.closest('tr').remove()" class="text-red-500">&times;</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" onclick="addRow()" class="mt-4 text-sm text-indigo-600 font-semibold hover:text-indigo-800">+ Tambah Baris Realisasi</button>
                        </div>
                    </div>

                    <!-- LAMPIRAN BUKTI & DOKUMENTASI -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">3</span>
                            <h3 class="text-xl font-bold text-gray-800">Lampiran Bukti & Dokumentasi</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <x-input-label for="bukti_pembayaran" :value="__('Upload Kwitansi / Bukti Pembayaran')" />
                                <input id="bukti_pembayaran" name="bukti_pembayaran[]" type="file" multiple accept="image/*,.pdf" class="mt-2 block w-full text-sm" />
                                <p class="mt-1 text-xs text-gray-500">Bisa pilih banyak file sekaligus (Gambar/PDF).</p>
                            </div>
                            <div class="p-4 bg-gray-500 rounded-lg border border-dashed border-gray-300">
                                <x-input-label for="foto_dokumentasi" :value="__('Upload Foto Dokumentasi Kegiatan')" />
                                <input id="foto_dokumentasi" name="foto_dokumentasi[]" type="file" multiple accept="image/*" class="mt-2 block w-full text-sm" />
                                <p class="mt-1 text-xs text-gray-500">Bisa pilih banyak foto kegiatan sekaligus.</p>
                            </div>
                        </div>
                    </div>

                    <!-- TANDA TANGAN -->
                    <div class="mb-12 p-6 bg-gray-50 rounded-lg border border-dashed border-gray-300"
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
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16"/></svg>
                                <h3 class="text-lg font-bold text-gray-800">Tanda Tangan</h3>
                            </div>
                            <button type="button" @click="addSigner()"
                                class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 border border-indigo-300 rounded-lg px-3 py-1.5 hover:bg-indigo-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Penandatangan
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Penandatangan internal memakai nama dari profil dan mendapat tanda tangan digital. Pilih "Pihak Luar" untuk nama di luar sistem.</p>

                        <template x-for="(signer, idx) in signers" :key="idx">
                            <div class="flex gap-3 items-start mb-3 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="flex-none">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold" x-text="idx + 1"></span>
                                </div>
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis</label>
                                        <select :name="'penandatangan_jenis[' + idx + ']'"
                                                x-model="signer.jenis"
                                                @change="onJenisChange(idx)"
                                                class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            <option value="internal">Internal (dari profil)</option>
                                            <option value="eksternal">Pihak Luar (manual)</option>
                                        </select>
                                    </div>
                                    <div x-show="signer.jenis === 'internal'">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan</label>
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
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan / Keterangan</label>
                                        <input type="text"
                                               :name="'penandatangan_jabatan[' + idx + ']'"
                                               x-model="signer.jabatan"
                                               placeholder="Contoh: Pembina UKM"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-sm" />
                                    </div>
                                    <div :class="signer.jenis === 'internal' ? '' : 'sm:col-span-2'">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama</label>
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
                                            class="p-1.5 text-gray-400 hover:text-red-500 rounded transition-colors"
                                            title="Hapus penandatangan ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div x-show="signers.length === 1" class="w-7 h-7"></div>
                                </div>
                            </div>
                        </template>

                        <p class="mt-4 text-xs text-gray-500 italic">Penandatangan internal mendapat tanda tangan digital yang dapat diverifikasi via QR code pada dokumen yang dicetak.</p>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <x-secondary-button type="submit" name="action" value="draft" class="px-6 py-2">
                            Simpan Draft
                        </x-secondary-button>
                        <x-primary-button type="submit" name="action" value="print" class="px-8 py-3 text-lg">
                            Simpan & Cetak LPJ
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowIdx = 1;
        function addRow() {
            const tbody = document.getElementById('realisasi-body');
            const row = `
                <tr class="border-b">
                    <td class="p-2"><input type="text" name="realisasi_items[${rowIdx}][uraian]" class="w-full border-gray-300 rounded-sm"></td>
                    <td class="p-2"><input type="number" name="realisasi_items[${rowIdx}][estimasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                    <td class="p-2"><input type="number" name="realisasi_items[${rowIdx}][realisasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                    <td class="p-2"><input type="text" name="realisasi_items[${rowIdx}][keterangan]" class="w-full border-gray-300 rounded-sm"></td>
                    <td class="p-2 text-center">
                        <button type="button" onclick="this.closest('tr').remove()" class="text-red-500">&times;</button>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
            rowIdx++;
        }
    </script>
</x-app-layout>
