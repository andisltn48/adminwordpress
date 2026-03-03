<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg shadow-red-500/20 hover:bg-red-700 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-500/10']) }}>
    {{ $slot }}
</button>
