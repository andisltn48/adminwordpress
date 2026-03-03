<x-app-layout>
    <div class="py-12">
        <!-- Premium Header -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <div>
                    <h2 class="font-bold text-4xl text-slate-800 tracking-tight">
                        {{ __('Manajemen Berita') }}
                    </h2>
                    <p class="mt-2 text-slate-500">
                        {{ __('Kelola konten berita dan distribusinya ke berbagai website aktif.') }}
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('beritas.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-primary-700 active:scale-95 transition-all duration-300 shadow-xl shadow-primary-500/25 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
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
            var table = $('#beritas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('beritas.index') }}",
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
                        width: '10%'
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
        });
    </script>
</x-app-layout>
