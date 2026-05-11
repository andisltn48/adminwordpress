<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-rose-100 rounded-xl">
                    <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                {{ __('Laravel Failed Jobs') }}
            </div>
            <div class="flex gap-2">
                <form action="{{ route('failed-jobs.retry-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Retry All
                    </button>
                </form>
                <form action="{{ route('failed-jobs.delete-all') }}" method="POST" onsubmit="return confirm('Hapus semua failed jobs?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-bold hover:bg-rose-700 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Flush All
                    </button>
                </form>
            </div>
        </h2>
    </x-slot>

    <div class="py-12" x-data="failedJobHandler()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Daftar Failed Jobs (Queue)</h3>
                        <p class="text-slate-500 text-sm mt-1">Daftar job yang gagal dieksekusi oleh queue worker.</p>
                    </div>

                    <table id="failed-jobs-table" class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Queue</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Exception</th>
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

                <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                    
                    <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-slate-800">Detail Failed Job</h3>
                        <button @click="showDetail = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Connection</label>
                                <p class="text-sm font-semibold text-slate-700" x-text="selectedJob.connection"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Queue</label>
                                <p class="text-sm font-semibold text-slate-700" x-text="selectedJob.queue"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Failed At</label>
                                <p class="text-sm font-semibold text-slate-700" x-text="selectedJob.failed_at"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Payload</label>
                            <div class="p-4 bg-slate-900 rounded-xl overflow-x-auto">
                                <pre class="text-xs text-emerald-400 font-mono" x-text="JSON.stringify(JSON.parse(selectedJob.payload), null, 2)"></pre>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Exception</label>
                            <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl">
                                <pre class="text-xs text-rose-700 whitespace-pre-wrap font-mono" x-text="selectedJob.exception"></pre>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-slate-100 gap-3">
                            <button @click="showDetail = false" class="px-6 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function failedJobHandler() {
            return {
                showDetail: false, 
                selectedJob: {
                    id: '',
                    connection: '',
                    queue: '',
                    failed_at: '',
                    payload: '{}',
                    exception: ''
                },
                init() {
                    window.failedJobModal = this;
                }
            }
        }

        $(document).ready(function() {
            let table = $('#failed-jobs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('failed-jobs.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'queue', name: 'queue', width: '15%' },
                    { data: 'exception', name: 'exception', width: '50%' },
                    { data: 'failed_at', name: 'failed_at', width: '15%' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '15%' }
                ],
                language: {
                    "emptyTable": "Tidak ada failed jobs ditemukan",
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
            $('#failed-jobs-table').on('click', '.btn-detail-job', function() {
                let data = $(this).data();
                if (window.failedJobModal) {
                    window.failedJobModal.selectedJob = {
                        id: data.id,
                        connection: data.connection,
                        queue: data.queue,
                        failed_at: data.failedAt,
                        payload: data.payload,
                        exception: data.exception
                    };
                    window.failedJobModal.showDetail = true;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
