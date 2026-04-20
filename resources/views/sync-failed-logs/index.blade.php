<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-3">
            <div class="p-2 bg-rose-100 rounded-xl">
                <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            {{ __('Sync Gagal') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="syncLogHandler()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Daftar Sync yang Gagal</h3>
                        <p class="text-slate-500 text-sm mt-1">Gunakan tombol detail untuk melihat log lengkap dan mencoba sync ulang.</p>
                    </div>

                    <table id="sync-failed-logs-table" class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Berita</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Website</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Error</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div x-show="showDetail" 
            class="fixed inset-0 z-[60] overflow-y-auto" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm" @click="showDetail = false"></div>

                <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                    
                    <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-slate-800">Detail Log Kegagalan</h3>
                        <button @click="showDetail = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Berita</label>
                                <p class="text-sm font-semibold text-slate-700" x-text="selectedLog.berita"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Website Target</label>
                                <p class="text-sm font-semibold text-slate-700" x-text="selectedLog.website"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pesan Error</label>
                            <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-sm text-rose-700 font-medium" x-text="selectedLog.error"></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Response Body</label>
                            <div class="p-4 bg-slate-900 rounded-xl">
                                <pre class="text-xs text-slate-300 overflow-x-auto whitespace-pre-wrap font-mono" x-text="selectedLog.response || '-'"></pre>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <div class="text-xs text-slate-400">
                                Terjadi pada: <span class="font-bold text-slate-600" x-text="selectedLog.time"></span>
                            </div>
                            <div class="flex gap-3">
                                <button @click="showDetail = false" class="px-6 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 transition-all">
                                    Tutup
                                </button>
                                <form :action="selectedLog.retryUrl" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Coba Ulang Sync
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function syncLogHandler() {
            return {
                showDetail: false, 
                selectedLog: {
                    berita: '',
                    website: '',
                    error: '',
                    response: '',
                    status: '',
                    time: '',
                    retryUrl: ''
                },
                init() {
                    window.syncLogModal = this;
                }
            }
        }

        $(document).ready(function() {
            let table = $('#sync-failed-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('sync-failed-logs.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'berita.judul', name: 'berita.judul', width: '25%' },
                    { data: 'website.nama_website', name: 'website.nama_website', width: '15%' },
                    { data: 'error_message', name: 'sync_failed_logs.error_message', width: '25%' },
                    { data: 'status', name: 'sync_failed_logs.status', width: '12%' },
                    { data: 'created_at', name: 'sync_failed_logs.created_at', width: '13%' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '5%' }
                ],
                language: {
                    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Sedang memuat...",
                    "processing": "Sedang memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Handle Detail Button Click
            $('#sync-failed-logs-table').on('click', '.btn-detail', function() {
                let data = $(this).data();
                
                // If jQuery auto-parsed the JSON string into an object, stringify it for display
                let displayResponse = typeof data.response === 'object' 
                    ? JSON.stringify(data.response, null, 2) 
                    : data.response;

                if (window.syncLogModal) {
                    window.syncLogModal.selectedLog = {
                        berita: data.berita,
                        website: data.website,
                        error: data.error,
                        response: displayResponse,
                        status: data.status,
                        time: data.time,
                        retryUrl: data.retryUrl
                    };
                    window.syncLogModal.showDetail = true;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
