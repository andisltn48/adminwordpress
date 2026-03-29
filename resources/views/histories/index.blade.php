<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-xl">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                {{ __('Riwayat Pembuatan Berita') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        startDate: '{{ request('start_date', '') }}',
        endDate: '{{ request('end_date', '') }}',
        get exportUrl() {
            let url = '{{ route('histories.export') }}';
            let params = [];
            if (this.startDate) params.push('start_date=' + this.startDate);
            if (this.endDate) params.push('end_date=' + this.endDate);
            return params.length ? url + '?' + params.join('&') : url;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter Bar --}}
            <div class="premium-card mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('histories.index') }}" class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">{{ __('Dari Tanggal') }}</label>
                            <input type="date" name="start_date" x-model="startDate"
                                value="{{ request('start_date') }}"
                                class="block w-full border-slate-200 focus:border-primary-500 focus:ring-primary-500 rounded-xl shadow-sm transition-all duration-300 py-2.5 px-4 text-sm" />
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">{{ __('Sampai Tanggal') }}</label>
                            <input type="date" name="end_date" x-model="endDate"
                                value="{{ request('end_date') }}"
                                class="block w-full border-slate-200 focus:border-primary-500 focus:ring-primary-500 rounded-xl shadow-sm transition-all duration-300 py-2.5 px-4 text-sm" />
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-primary-200 gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                {{ __('Filter') }}
                            </button>
                            @if(request('start_date') || request('end_date'))
                                <a href="{{ route('histories.index') }}"
                                    class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 active:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('Reset') }}
                                </a>
                            @endif
                            <a :href="exportUrl"
                                class="inline-flex items-center px-5 py-2.5 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-emerald-200 gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Export Excel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="premium-card overflow-hidden">
                <div class="p-8">
                    <div class="table-responsive">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-left text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    <th class="px-6 py-4">{{ __('Judul Berita') }}</th>
                                    <th class="px-6 py-4">{{ __('Portal Website') }}</th>
                                    <th class="px-6 py-4">{{ __('Status') }}</th>
                                    <th class="px-6 py-4">{{ __('Oleh') }}</th>
                                    <th class="px-6 py-4">{{ __('Waktu') }}</th>
                                    <th class="px-6 py-4 text-center">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                    <tr
                                        class="bg-white hover:bg-slate-50 transition-all duration-300 group shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] rounded-2xl overflow-hidden">
                                        <td class="px-6 py-5 first:rounded-l-2xl">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-slate-700 text-sm group-hover:text-primary-600 transition-colors">{{ $history->judul }}</span>
                                                <span class="text-[10px] text-slate-400 mt-0.5">ID Berita:
                                                    #{{ $history->berita_id }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                                                    {{ substr($history->website->nama_website, 0, 1) }}
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-slate-600">{{ $history->website->nama_website }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            @if ($history->status === 'Published')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-tighter border border-emerald-200">
                                                    {{ __('Published') }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase tracking-tighter border border-amber-200">
                                                    {{ __('Draft') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 text-xs text-slate-600 font-medium">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-[10px] text-indigo-600 font-black uppercase">
                                                    {{ substr($history->user->name, 0, 1) }}
                                                </div>
                                                {{ $history->user->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-xs text-slate-400 font-medium">
                                            {{ $history->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-5 last:rounded-r-2xl text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $history->detail_url }}" target="_blank"
                                                    class="p-2 hover:bg-primary-50 text-slate-400 hover:text-primary-600 rounded-xl transition-all"
                                                    title="Lihat di Portal">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('beritas.show', $history->berita_id) }}"
                                                    class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-all"
                                                    title="Detail Admin">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center justify-center grayscale opacity-30">
                                                <svg class="w-20 h-20 mb-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span
                                                    class="text-xl font-black uppercase tracking-widest text-slate-400">{{ __('Belum Ada Riwayat') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
