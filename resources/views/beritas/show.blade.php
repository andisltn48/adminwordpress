<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-3">
                <div class="p-2 bg-primary-100 rounded-xl">
                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12h10M7 16h10" />
                    </svg>
                </div>
                {{ __('Detail Berita') }}
            </h2>
            <a href="{{ route('beritas.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 active:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Content Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Main Content Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                        @if ($berita->featured_image)
                            <div class="relative h-64 sm:h-96 w-full overflow-hidden">
                                <img src="{{ asset('storage/' . $berita->featured_image) }}"
                                    class="w-full h-full object-cover" alt="{{ $berita->judul }}">
                                <div
                                    class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/60 to-transparent">
                                </div>
                                <div class="absolute bottom-6 left-8">
                                    <span
                                        class="px-3 py-1 bg-primary-500 text-white text-xs font-bold rounded-full shadow-lg">
                                        {{ $berita->status }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="p-8">
                            <h1 class="text-3xl font-extrabold text-slate-900 mb-6 leading-tight">
                                {{ $berita->judul }}
                            </h1>

                            <div class="prose prose-slate max-w-none prose-img:rounded-2xl prose-img:shadow-lg">
                                {!! $berita->konten !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="space-y-8">
                    <!-- Distribution Card -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9h18" />
                            </svg>
                            {{ __('Distribusi Website') }}
                        </h3>

                        <div class="space-y-3">
                            @foreach ($berita->websites as $web)
                                <div
                                    class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-200 transition-colors group">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold text-sm text-slate-800">{{ $web->nama_website }}</span>
                                        <span
                                            class="p-1.5 bg-white rounded-lg border border-slate-200 group-hover:border-primary-300 transition-colors">
                                            <svg class="w-3 h-3 text-slate-400 group-hover:text-primary-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">URL
                                            Detail:</span>
                                        <a href="{{ $web->pivot->website_url . $web->pivot->detail_url }}" target="_blank"
                                            class="text-xs text-primary-600 font-medium hover:underline break-all">
                                            {{ $web->pivot->website_url . $web->pivot->detail_url }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            @if ($berita->websites->isEmpty())
                                <div class="text-center py-6">
                                    <div
                                        class="mx-auto w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mb-3 text-slate-400">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500 italic">Belum ada distribusi website.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Info Card -->
                    <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl shadow-slate-200">
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Kategori</span>
                                <p class="text-sm font-medium mt-1">
                                    @if($berita->kategori)
                                        <span class="px-3 py-1 bg-violet-500/20 text-violet-300 text-xs font-bold rounded-full">{{ $berita->kategori }}</span>
                                    @else
                                        <span class="text-slate-500 italic">Belum ada kategori</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Tanggal
                                    Publikasi</span>
                                <p class="text-sm font-medium mt-1">
                                    {{ $berita->tanggal_publikasi ? $berita->tanggal_publikasi->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Dibuat
                                    Pada</span>
                                <p class="text-sm font-medium mt-1">{{ $berita->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Terakhir
                                    Diupdate</span>
                                <p class="text-sm font-medium mt-1">{{ $berita->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
