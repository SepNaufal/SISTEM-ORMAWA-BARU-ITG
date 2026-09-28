<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Kelola Aspirasi & Suara Mahasiswa</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Aspirasi Bertiket Mahasiswa (Portal Layanan) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Aspirasi Mahasiswa Bertiket (Portal Terpadu)</h3>
                        <p class="text-xs text-slate-500">Aspirasi publik mahasiswa melalui sistem kode tiket. BPM dapat menindaklanjuti atau meneruskan ke BKHM.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-3 text-left border">Kode Tiket</th>
                                <th class="p-3 text-left border">Mahasiswa</th>
                                <th class="p-3 text-left border">Judul & Aspirasi</th>
                                <th class="p-3 text-center border">Status</th>
                                <th class="p-3 text-center border">Aksi BPM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tiketAspirasis as $t)
                                <tr class="border-b hover:bg-slate-50/60 transition">
                                    <td class="p-3 border font-mono font-bold text-xs text-indigo-700">
                                        {{ $t->kode_tiket }}
                                    </td>
                                    <td class="p-3 border">
                                        <div class="font-bold text-xs text-slate-800">{{ $t->nama_mahasiswa }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $t->nim }} &bull; {{ $t->prodi ?? 'ITG' }}</div>
                                    </td>
                                    <td class="p-3 border">
                                        <div class="font-bold text-slate-900 text-xs">{{ $t->judul }}</div>
                                        <div class="text-xs text-slate-600 mt-0.5">{{ Str::limit($t->isi, 90) }}</div>
                                        @if($t->catatan_bpm)
                                            <div class="mt-1 text-[11px] text-indigo-700 bg-indigo-50 p-1.5 rounded">
                                                <strong>Catatan BPM:</strong> {{ $t->catatan_bpm }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3 border text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $t->status_color }}">
                                            {{ $t->status_label }}
                                        </span>
                                    </td>
                                    <td class="p-3 border text-center space-y-1">
                                        @if($t->status !== 'diteruskan_ke_bkhm' && $t->status !== 'selesai')
                                            <button type="button" onclick="openTeruskanModal('{{ $t->id }}', '{{ $t->kode_tiket }}')"
                                                class="inline-flex items-center justify-center min-h-[44px] px-3 text-xs font-bold rounded-lg text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition">
                                                Teruskan ke BKHM &rarr;
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Sudah diteruskan</span>
                                        @endif
                                        @if($t->lampiran)
                                            <div>
                                                <a href="{{ route('layanan.lampiran', $t) }}" target="_blank" class="text-[11px] text-indigo-600 hover:underline">
                                                    Lampiran
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 italic">Belum ada aspirasi bertiket yang masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tiketAspirasis->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Teruskan ke BKHM -->
    <div id="modal-teruskan" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeTeruskanModal()"></div>
            <div class="bg-white rounded-2xl shadow-xl z-10 max-w-lg w-full p-6 relative border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-1">Teruskan Aspirasi ke BKHM</h3>
                <p class="text-xs text-slate-500 mb-4" id="teruskan-kode-label">Kode Tiket: -</p>
                <form id="form-teruskan" method="POST" action="">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="catatan_bpm_teruskan" :value="__('Catatan Rekomendasi BPM untuk BKHM *')" />
                            <textarea name="catatan_bpm" id="catatan_bpm_teruskan" rows="4" required class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tuliskan urgensi, rekomendasi tindak lanjut, atau dasar pertimbangan dari BPM..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6 gap-2">
                        <button type="button" onclick="closeTeruskanModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
                        <x-primary-button class="rounded-xl">Teruskan ke BKHM</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTeruskanModal(id, kodeTiket) {
            document.getElementById('modal-teruskan').style.display = 'block';
            document.getElementById('form-teruskan').action = '{{ url('bpm/aspirasi') }}/' + id + '/teruskan';
            document.getElementById('teruskan-kode-label').textContent = 'Tiket: ' + kodeTiket;
        }
        function closeTeruskanModal() {
            document.getElementById('modal-teruskan').style.display = 'none';
        }
    </script>
</x-app-layout>
