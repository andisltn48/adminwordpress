<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-xl">
                <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- News Stats Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 p-8 flex flex-col justify-between relative">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <svg class="w-32 h-32 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-6 text-primary-600">
                            <div class="p-2 bg-primary-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span
                                class="font-extrabold text-sm uppercase tracking-widest">{{ __('Statistik Berita') }}</span>
                        </div>

                        <div class="flex items-end gap-3 mb-8">
                            <span
                                class="text-6xl font-black text-slate-900 leading-none">{{ $beritaStats['total'] }}</span>
                            <span
                                class="text-xl font-bold text-slate-400 pb-1 leading-none">{{ __('Total Berita') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
                            <span
                                class="text-xs font-bold text-emerald-600 uppercase tracking-tighter">{{ __('Published') }}</span>
                            <p class="text-2xl font-black text-emerald-700 leading-tight mt-1">
                                {{ $beritaStats['published'] }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100">
                            <span
                                class="text-xs font-bold text-amber-600 uppercase tracking-tighter">{{ __('Draft') }}</span>
                            <p class="text-2xl font-black text-amber-700 leading-tight mt-1">{{ $beritaStats['draft'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Website Stats Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 p-8 flex flex-col justify-between relative">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <svg class="w-32 h-32 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9h18" />
                        </svg>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-6 text-indigo-600">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9h18" />
                                </svg>
                            </div>
                            <span
                                class="font-extrabold text-sm uppercase tracking-widest">{{ __('Portal Website') }}</span>
                        </div>

                        <div class="flex items-end gap-3 mb-8">
                            <span
                                class="text-6xl font-black text-slate-900 leading-none">{{ $websiteStats['total'] }}</span>
                            <span
                                class="text-xl font-bold text-slate-400 pb-1 leading-none">{{ __('Total Portal') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                            <span
                                class="text-xs font-bold text-indigo-600 uppercase tracking-tighter">{{ __('Aktif') }}</span>
                            <p class="text-2xl font-black text-indigo-700 leading-tight mt-1">
                                {{ $websiteStats['aktif'] }}</p>
                        </div>
                        <div class="bg-rose-50 rounded-2xl p-4 border border-rose-100">
                            <span
                                class="text-xs font-bold text-rose-600 uppercase tracking-tighter">{{ __('Tidak Aktif') }}</span>
                            <p class="text-2xl font-black text-rose-700 leading-tight mt-1">
                                {{ $websiteStats['tidak_aktif'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Welcome Card -->
            <div class="bg-slate-900 overflow-hidden shadow-2xl sm:rounded-3xl p-10 text-white relative">
                <div class="relative z-10 w-full md:w-2/3">
                    <h1 class="text-4xl font-black mb-4 leading-tight">Selamat Datang di Admin Portal! 👋</h1>
                    <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                        Kelola berita Anda hari ini dengan sekali klik. Semua distribusi ke berbagai portal website
                        sudah terintegrasi dan terpantau secara real-time.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('beritas.create') }}"
                            class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-2xl font-bold transition-all shadow-lg shadow-primary-500/25">
                            Buat Berita Baru
                        </a>
                        <a href="{{ route('websites.index') }}"
                            class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-bold transition-all backdrop-blur-sm">
                            Kelola Website
                        </a>
                    </div>
                </div>

                <div class="absolute -right-20 -bottom-20 opacity-10">
                    <svg class="w-96 h-96 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
