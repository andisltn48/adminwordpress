<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg shadow-primary-500/20 hover:bg-primary-700 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary-500/10']) }}>
    {{ $slot }}
</button>
