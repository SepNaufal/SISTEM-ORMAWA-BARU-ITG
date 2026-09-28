<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.konfigurasi.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Kolom Kiri: Info Umum -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold border-b pb-2">Informasi Umum</h3>
                                
                                <div>
                                    <x-input-label for="nama_aplikasi" value="Nama Aplikasi / Sistem" />
                                    <x-text-input id="nama_aplikasi" name="nama_aplikasi" type="text" class="mt-1 block w-full" value="{{ $konfigurasis['nama_aplikasi'] ?? '' }}" required />
                                </div>

                                <div>
                                    <x-input-label for="logo_sistem" value="Logo Sistem (Opsional, PNG/JPG max 2MB)" />
                                    @if(isset($konfigurasis['logo_sistem']) && $konfigurasis['logo_sistem'])
                                        <div class="my-2">
                                            <img src="{{ Storage::url($konfigurasis['logo_sistem']) }}" alt="Logo" class="h-16 object-contain">
                                        </div>
                                    @endif
                                    <input id="logo_sistem" name="logo_sistem" type="file" class="mt-1 block w-full border border-gray-300 rounded p-1" accept="image/*" />
                                </div>
                            </div>

                            <!-- Kolom Kanan: Kop Surat -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold border-b pb-2">Kop Surat Resmi</h3>
                                
                                <div>
                                    <x-input-label for="kop_baris1" value="Kop Surat - Baris 1 (Instansi Induk)" />
                                    <x-text-input id="kop_baris1" name="kop_baris1" type="text" class="mt-1 block w-full" value="{{ $konfigurasis['kop_baris1'] ?? '' }}" />
                                </div>
                                
                                <div>
                                    <x-input-label for="kop_baris2" value="Kop Surat - Baris 2 (Nama Institusi)" />
                                    <x-text-input id="kop_baris2" name="kop_baris2" type="text" class="mt-1 block w-full font-bold" value="{{ $konfigurasis['kop_baris2'] ?? '' }}" />
                                </div>

                                <div>
                                    <x-input-label for="kop_baris3" value="Kop Surat - Baris 3 (Alamat)" />
                                    <x-text-input id="kop_baris3" name="kop_baris3" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['kop_baris3'] ?? '' }}" />
                                </div>

                                <div>
                                    <x-input-label for="kop_baris4" value="Kop Surat - Baris 4 (Kontak/Web)" />
                                    <x-text-input id="kop_baris4" name="kop_baris4" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['kop_baris4'] ?? '' }}" />
                                </div>

                                <div>
                                    <x-input-label for="kop_logo" value="Logo Kop Surat (Opsional, PNG/JPG max 2MB)" />
                                    @if(isset($konfigurasis['kop_logo']) && $konfigurasis['kop_logo'])
                                        <div class="my-2">
                                            <img src="{{ Storage::url($konfigurasis['kop_logo']) }}" alt="Logo Kop" class="h-16 object-contain bg-gray-100 p-1">
                                        </div>
                                    @endif
                                    <input id="kop_logo" name="kop_logo" type="file" class="mt-1 block w-full border border-gray-300 rounded p-1" accept="image/*" />
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Pejabat Penandatangan Resmi Kampus -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Pejabat Penandatangan Resmi Kampus</h3>
                                <p class="text-xs text-gray-500">Identitas pejabat yang digunakan secara otomatis pada persuratan, pengesahan LPJ, Surat Peringatan (SP), dan rekomendasi resmi.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Wakil Rektor III -->
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-3">
                                    <h4 class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
                                        <span>Wakil Rektor III (Bidang Kemahasiswaan)</span>
                                    </h4>

                                    <div>
                                        <x-input-label for="wr3_nama" value="Nama Lengkap & Gelar WR3" />
                                        <x-text-input id="wr3_nama" name="wr3_nama" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['wr3_nama'] ?? 'Pejabat Wakil Rektor III' }}" />
                                    </div>

                                    <div>
                                        <x-input-label for="wr3_nidn" value="NIDN WR3" />
                                        <x-text-input id="wr3_nidn" name="wr3_nidn" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $konfigurasis['wr3_nidn'] ?? '-' }}" />
                                    </div>

                                    <div>
                                        <x-input-label for="wr3_jabatan" value="Nama Jabatan Resmi WR3" />
                                        <x-text-input id="wr3_jabatan" name="wr3_jabatan" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['wr3_jabatan'] ?? 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama' }}" />
                                    </div>
                                </div>

                                <!-- Kepala BKHM -->
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-3">
                                    <h4 class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
                                        <span>Kepala BKHM (Biro Kemahasiswaan & Hubungan Masyarakat)</span>
                                    </h4>

                                    <div>
                                        <x-input-label for="bkhm_nama" value="Nama Lengkap & Gelar Kepala BKHM" />
                                        <x-text-input id="bkhm_nama" name="bkhm_nama" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['bkhm_nama'] ?? 'Pejabat Kepala BKHM' }}" />
                                    </div>

                                    <div>
                                        <x-input-label for="bkhm_nidn" value="NIDN Kepala BKHM" />
                                        <x-text-input id="bkhm_nidn" name="bkhm_nidn" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $konfigurasis['bkhm_nidn'] ?? '-' }}" />
                                    </div>

                                    <div>
                                        <x-input-label for="bkhm_jabatan" value="Nama Jabatan Resmi Kepala BKHM" />
                                        <x-text-input id="bkhm_jabatan" name="bkhm_jabatan" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['bkhm_jabatan'] ?? 'Kepala Biro Kemahasiswaan dan Hubungan Masyarakat (BKHM)' }}" />
                                    </div>
                                </div>

                                <!-- Bendahara Kampus ITG -->
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-3 md:col-span-2">
                                    <h4 class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
                                        <span>Bendahara Kampus ITG</span>
                                    </h4>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <x-input-label for="bendahara_nama" value="Nama Lengkap & Gelar Bendahara" />
                                            <x-text-input id="bendahara_nama" name="bendahara_nama" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['bendahara_nama'] ?? 'Bendahara Kampus ITG' }}" />
                                        </div>

                                        <div>
                                            <x-input-label for="bendahara_nidn" value="NIDN Bendahara" />
                                            <x-text-input id="bendahara_nidn" name="bendahara_nidn" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $konfigurasis['bendahara_nidn'] ?? '-' }}" />
                                        </div>

                                        <div>
                                            <x-input-label for="bendahara_jabatan" value="Nama Jabatan Resmi Bendahara" />
                                            <x-text-input id="bendahara_jabatan" name="bendahara_jabatan" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['bendahara_jabatan'] ?? 'Bendahara Kampus ITG' }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-4">
                            <x-primary-button>
                                {{ __('Simpan Pengaturan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>