<x-app-layout>
    <div x-data="{
        openModal: {{ $errors->isNotEmpty() ? 'true' : 'false' }},
        openEditModal: false,
        editData: {
            id: '',
            nama_website: '',
            url: '',
            status: true,
            updateUrl: ''
        },
        editWebsite(data) {
            this.editData = data;
            this.openEditModal = true;
        }
    }" class="py-12">

        <!-- Premium Header inside Alpine Scope -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <div>
                    <h2 class="font-bold text-4xl text-slate-800 tracking-tight">
                        {{ __('Websites') }}
                    </h2>
                    <p class="mt-2 text-slate-500">
                        {{ __('Kelola informasi dan status website WordPress Anda secara terpusat.') }}
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative flex-1 md:flex-initial">
                        <select id="status-filter"
                            class="w-full px-4 py-3 bg-white border-slate-200 text-slate-600 font-semibold rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none appearance-none pr-10">
                            <option value="">{{ __('Semua Status') }}</option>
                            <option value="1">{{ __('Aktif') }}</option>
                            <option value="0">{{ __('Non-aktif') }}</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            onclick="window.location.href='{{ route('websites.export') }}?status=' + $('#status-filter').val()"
                            class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-emerald-700 active:scale-95 transition-all duration-300 shadow-xl shadow-emerald-500/25 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('Export Excel') }}
                        </button>
                        <button @click="openModal = true"
                            class="inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-primary-700 active:scale-95 transition-all duration-300 shadow-xl shadow-primary-500/25 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Tambah Website') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div>
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

                <div class="premium-card overflow-hidden">
                    <div class="p-6">
                        <div class="table-responsive">
                            <table id="websites-table" class="w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Nama Website') }}</th>
                                        <th>{{ __('URL') }}</th>
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

        <!-- Create Website Modal -->
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Backdrop -->
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="openModal = false">
            </div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="openModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-xl p-8 overflow-hidden transition-all transform bg-white shadow-2xl rounded-3xl"
                    @click.away="openModal = false">

                    <header class="mb-8">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-slate-800">
                                {{ __('Tambah Website Baru') }}
                            </h2>
                            <button @click="openModal = false"
                                class="p-2 transition-colors rounded-full hover:bg-slate-100 text-slate-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-slate-500">
                            {{ __('Isi detail di bawah ini untuk menambahkan situs WordPress baru ke dasbor Anda.') }}
                        </p>
                    </header>

                    <form action="{{ route('websites.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nama_website" :value="__('Nama Website')" />
                            <x-text-input id="nama_website" name="nama_website" type="text"
                                class="block w-full mt-1" :value="old('nama_website')" required autofocus
                                placeholder="mis. Blog Saya" />
                            <x-input-error class="mt-2" :messages="$errors->get('nama_website')" />
                        </div>

                        <div>
                            <x-input-label for="url" :value="__('Website URL')" />
                            <x-text-input id="url" name="url" type="url" class="block w-full mt-1"
                                :value="old('url')" required placeholder="https://example.com" />
                            <x-input-error class="mt-2" :messages="$errors->get('url')" />
                        </div>

                        <div class="flex items-center gap-2 py-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="1" class="sr-only peer"
                                    {{ old('status', true) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600">
                                </div>
                                <span
                                    class="ml-3 text-sm font-semibold text-slate-700">{{ __('Status Website Aktif') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-slate-100">
                            <button type="button" @click="openModal = false"
                                class="px-5 py-2.5 bg-white text-slate-600 border border-slate-200 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit"
                                class="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all transform active:scale-95">
                                {{ __('Simpan Website') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Website Modal -->
        <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Backdrop -->
            <div x-show="openEditModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm"
                @click="openEditModal = false">
            </div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="openEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-xl p-8 overflow-hidden transition-all transform bg-white shadow-2xl rounded-3xl"
                    @click.away="openEditModal = false">

                    <header class="mb-8">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-slate-800">
                                {{ __('Edit Website') }}
                            </h2>
                            <button @click="openEditModal = false"
                                class="p-2 transition-colors rounded-full hover:bg-slate-100 text-slate-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-slate-500">
                            {{ __('Perbarui detail situs WordPress Anda.') }}
                        </p>
                    </header>

                    <form :action="editData.updateUrl" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="edit_nama_website" :value="__('Nama Website')" />
                            <x-text-input id="edit_nama_website" name="nama_website" type="text"
                                class="block w-full mt-1" x-model="editData.nama_website" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('nama_website')" />
                        </div>

                        <div>
                            <x-input-label for="edit_url" :value="__('Website URL')" />
                            <x-text-input id="edit_url" name="url" type="url" class="block w-full mt-1"
                                x-model="editData.url" required />
                            <x-input-error class="mt-2" :messages="$errors->get('url')" />
                        </div>

                        <div class="flex items-center gap-2 py-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="1" class="sr-only peer"
                                    x-model="editData.status">
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600">
                                </div>
                                <span
                                    class="ml-3 text-sm font-semibold text-slate-700">{{ __('Status Website Aktif') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-slate-100">
                            <button type="button" @click="openEditModal = false"
                                class="px-5 py-2.5 bg-white text-slate-600 border border-slate-200 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit"
                                class="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all transform active:scale-95">
                                {{ __('Perbarui Website') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#websites-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('websites.index') }}",
                    data: function(d) {
                        d.status = $('#status-filter').val();
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
                        data: 'nama_website',
                        name: 'nama_website',
                        className: 'font-semibold text-slate-900'
                    },
                    {
                        data: 'url',
                        name: 'url',
                        render: function(data, type, row) {
                            return `<a href="${data}" target="_blank" class="text-primary-600 hover:text-primary-700 hover:underline inline-flex items-center gap-1 font-medium">
                                    ${data.length > 40 ? data.substring(0, 40) + '...' : data}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '15%'
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
                    searchPlaceholder: "Cari website...",
                    lengthMenu: "_MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        next: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                        previous: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>'
                    },
                    emptyTable: "Belum ada data website",
                    processing: '<div class="flex justify-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div></div>'
                },
                drawCallback: function() {
                    // Fix styling for DataTables elements on every draw
                    $('.dataTables_paginate').addClass('mt-6 flex justify-end');
                    $('.dataTables_filter').addClass('mb-4');
                }
            });

            $('#status-filter').change(function() {
                table.draw();
            });
        });
    </script>
</x-app-layout>
