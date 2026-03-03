<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-white border border-slate-200 rounded-xl font-bold text-sm text-slate-600 shadow-sm hover:bg-slate-50 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-slate-500/10 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
