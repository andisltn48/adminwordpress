<x-app-layout>
    <div class="py-12">
        <!-- Premium Header -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
            <div class="flex items-center justify-between gap-4">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-3">
                    <div class="p-2 bg-primary-100 rounded-xl">
                        <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12h10M7 16h10" />
                        </svg>
                    </div>
                    {{ __('Manajemen Berita') }}
                </h2>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 outline-none focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all">
                        <span
                            class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Filter Status:') }}</span>
                        <select id="filter-status"
                            class="text-sm font-bold text-slate-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer min-w-[120px]">
                            <option value="">{{ __('Semua Status') }}</option>
                            <option value="Published">{{ __('Published') }}</option>
                            <option value="Draft">{{ __('Draft') }}</option>
                        </select>
                    </div>
                    <a href="{{ route('beritas.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-primary-200 gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Berita') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-green-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-red-700">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="premium-card overflow-hidden">
                <div class="p-6">
                    <div class="table-responsive">
                        <table id="beritas-table" class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Gambar') }}</th>
                                    <th>{{ __('Judul Berita') }}</th>
                                    <th>{{ __('Website Target') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let table = $('#beritas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('beritas.index') }}",
                    data: function(d) {
                        d.status = $('#filter-status').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'featured_image',
                        name: 'featured_image',
                        orderable: false,
                        searchable: false,
                        width: '8%'
                    },
                    {
                        data: 'judul',
                        name: 'judul',
                        className: 'font-semibold text-slate-800'
                    },
                    {
                        data: 'target_websites',
                        name: 'target_websites',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '10%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-right'
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari berita...",
                    lengthMenu: "_MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        next: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                        previous: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>'
                    },
                    emptyTable: "Belum ada data berita",
                    processing: '<div class="flex justify-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div></div>'
                },
                drawCallback: function() {
                    $('.dataTables_paginate').addClass('mt-6 flex justify-end');
                    $('.dataTables_filter').addClass('mb-4');
                }
            });

            $('#filter-status').change(function() {
                table.draw();
            });
        });
    </script>
</x-app-layout>
