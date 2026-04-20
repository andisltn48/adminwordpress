<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Tailwind Play CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                colors: {
                    primary: {
                        50: '#f0f4ff',
                        100: '#e1e9ff',
                        200: '#c3d3ff',
                        300: '#a5bcff',
                        400: '#87a6ff',
                        500: '#4f46e5',
                        600: '#4338ca',
                        700: '#3730a3',
                        800: '#312e81',
                        900: '#1e1b4b',
                    },
                },
                boxShadow: {
                    'premium': '0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                    'premium-hover': '0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.03)',
                }
            }
        }
    }
</script>

<style type="text/tailwindcss">
    @layer components {
        .premium-card {
            @apply bg-white rounded-2xl border border-slate-200/60 shadow-premium;
        }
    }
</style>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans text-slate-900 antialiased selection:bg-primary-500 selection:text-white">
    <div class="fixed inset-0 -z-10 bg-white">
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-primary-50 via-transparent to-transparent opacity-70">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-blue-50 via-transparent to-transparent opacity-70">
        </div>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-8">
            <a href="/" class="flex items-center gap-2 group">
                <x-application-logo
                    class="w-12 h-12 fill-current text-primary-600 transition-transform group-hover:scale-110" />
                <span class="font-bold text-3xl tracking-tight text-slate-800">Admin<span
                        class="text-primary-600">WP</span></span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-10 py-10 premium-card overflow-hidden">
            <x-alert />
            {{ $slot }}
        </div>
    </div>
</body>

</html>
