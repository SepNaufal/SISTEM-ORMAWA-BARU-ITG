<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Master Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false, showEditModal: false, editRuangan: { id: null, nama_ruangan: '', kapasitas: 0, status_aktif: 1 } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold">Daftar Ruangan Kampus</h3>
                            <p class="text-sm text-gray-500">Kelola daftar ruangan, aula, dan laboratorium yang dapat dipinjam oleh Ormawa.</p>
                        </div>
                        <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            + Tambah Ruangan
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Ruangan</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kapasitas (Orang)</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($ruangans as $ruangan)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-gray-900">{{ $ruangan->nama_ruangan }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                            {{ $ruangan->kapasitas }} Mahasiswa
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($ruangan->status_aktif)
                                            <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Tersedia</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-semibold">Nonaktif / Renovasi</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <button @click="showEditModal = true; editRuangan = {{ json_encode($ruangan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">Edit</button>
                                        
                                        <form action="{{ route('sarpras.ruangan.destroy', $ruangan) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 text-sm font-semibold" onclick="return confirm('Yakin ingin menghapus ruangan ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">Belum ada data ruangan terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $ruangans->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Ruangan Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showAddModal = false"></div>
                <div class="bg-white rounded-lg shadow-xl z-10 max-w-md w-full p-6">
                    <h3 class="text-lg font-bold mb-4">Tambah Ruangan Baru</h3>
                    <form action="{{ route('sarpras.ruangan.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: Aula Gedung Rektorat atau Lab Komputer 3">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" min="1" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="100">
                        </div>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" name="status_aktif" id="status_aktif_add" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="status_aktif_add" class="ml-2 block text-sm text-gray-900">Ruangan Aktif / Tersedia untuk Dipinjam</label>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showAddModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Batal</button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Ruangan Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showEditModal = false"></div>
                <div class="bg-white rounded-lg shadow-xl z-10 max-w-md w-full p-6">
                    <h3 class="text-lg font-bold mb-4">Edit Data Ruangan</h3>
                    <form :action="'/sarpras/ruangan/' + editRuangan?.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" :value="editRuangan?.nama_ruangan" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" :value="editRuangan?.kapasitas" min="1" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" name="status_aktif" id="status_aktif_edit" value="1" :checked="editRuangan?.status_aktif" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="status_aktif_edit" class="ml-2 block text-sm text-gray-900">Ruangan Aktif / Tersedia untuk Dipinjam</label>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-sm transition-colors text-xs uppercase tracking-wider">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
