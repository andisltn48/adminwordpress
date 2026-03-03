<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="premium-card overflow-hidden">
                <div class="p-10 text-slate-700">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="relative">
                            <div class="absolute inset-0 bg-primary-200 blur-2xl opacity-20 animate-pulse"></div>
                            <div
                                class="relative p-6 bg-gradient-to-br from-primary-50 to-primary-100/50 text-primary-600 rounded-2xl border border-primary-200/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-center md:text-left">
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ __('Selamat datang kembali, :name!', ['name' => Auth::user()->name]) }}</h3>
                            <p class="text-lg text-slate-500 mt-2 max-w-xl">Pusat kendali WordPress Anda siap digunakan.
                                Kelola website Anda dengan cepat dan presisi.</p>
                            <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-3">
                                <a href="{{ route('websites.index') }}"
                                    class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20">
                                    Lihat Semua Website
                                </a>
                                <a href="{{ route('profile.edit') }}"
                                    class="inline-flex items-center px-5 py-2.5 bg-white text-slate-600 border border-slate-200 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                                    Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
