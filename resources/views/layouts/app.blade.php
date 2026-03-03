<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
                        'glass': '0 1px 3px rgba(0, 0, 0, 0.02)',
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            html {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                scroll-behavior: smooth;
            }

            ::-webkit-scrollbar {
                width: 10px;
            }

            ::-webkit-scrollbar-track {
                @apply bg-slate-50;
            }

            ::-webkit-scrollbar-thumb {
                @apply bg-slate-200 rounded-full border-2 border-slate-50 border-solid hover:bg-slate-300 transition-colors;
            }
        }

        @layer components {
            .glass-nav {
                @apply bg-white/70 backdrop-blur-xl border-b border-white/20 shadow-glass;
            }

            .premium-card {
                @apply bg-white rounded-2xl border border-slate-200/60 shadow-premium hover:shadow-premium-hover transition-all duration-300;
            }

            /* DataTables Premium Styling Overrides */
            .dataTables_wrapper .dataTables_length select {
                @apply px-4 py-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none;
                background-image: none;
            }

            .dataTables_wrapper .dataTables_filter input {
                @apply px-4 py-2 bg-slate-100 border-slate-300 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none ml-2;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                @apply bg-primary-600 !important;
                @apply text-white !important;
                @apply border-transparent !important;
                @apply rounded-xl !important;
                @apply font-bold !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current) {
                @apply border-transparent !important;
                @apply hover:bg-slate-100 !important;
                @apply hover:text-primary-600 !important;
                @apply rounded-xl !important;
                @apply transition-all !important;
            }

            .dataTables_wrapper .dataTables_info {
                @apply text-slate-500 text-sm font-medium pt-4 !important;
            }

            table.dataTable thead th {
                @apply border-b border-slate-100 font-bold text-slate-500 text-xs uppercase tracking-wider py-4 px-6 bg-slate-50/30 !important;
            }

            table.dataTable tbody td {
                @apply border-b border-slate-50 py-4 px-6 text-sm text-slate-600 !important;
            }

            table.dataTable.no-footer {
                @apply border-b-0 !important;
            }
        }
    </style>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased text-slate-900 selection:bg-primary-500 selection:text-white">
    <div class="fixed inset-0 -z-10 bg-white">
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-primary-50 via-transparent to-transparent opacity-70">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-blue-50 via-transparent to-transparent opacity-70">
        </div>
    </div>
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
