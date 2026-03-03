@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'px-4 py-3 bg-slate-100 border-slate-300 text-slate-700 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-xl transition-all duration-200 shadow-sm']) }}>
