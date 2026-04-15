<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AKAS POS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dominant text-slate-100 antialiased">
    {{-- layouts/app.blade.php --}}
    <div class="flex h-screen bg-dominant overflow-hidden">

        {{-- SIDEBAR (fixed 240px) --}}
        <aside class="w-60 bg-secondary border-r border-slate-700 flex flex-col flex-shrink-0">
            {{-- Logo --}}
            <div class="h-14 flex items-center px-5 border-b border-slate-700">
                <span class="text-accent font-bold text-lg tracking-tight">AKAS POS</span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 space-y-1 px-3">
                <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('dashboard') ? 'bg-slate-700 text-slate-100' : '' }}">
                    Dashboard
                </a>
                <a href="/master-stok" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('master-stok*') ? 'bg-slate-700 text-slate-100' : '' }}">
                    Master Stok
                </a>
                <a href="/sales" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('sales*') ? 'bg-slate-700 text-slate-100' : '' }}">
                    Transaksi
                </a>
                <a href="/shift" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('shift*') ? 'bg-slate-700 text-slate-100' : '' }}">
                    Shift
                </a>
                <a href="/reports" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('reports*') ? 'bg-slate-700 text-slate-100' : '' }}">
                    Laporan
                </a>
                <a href="/audit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('audit*') ? 'bg-slate-700 text-slate-100' : '' }}">
                    Audit Log
                </a>
            </nav>

            {{-- User info bottom --}}
            <div class="p-4 border-t border-slate-700">
                <span class="text-sm text-slate-400">{{ auth()->user()->nama_pegawai ?? 'Guest' }}</span>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- HEADER --}}
            <header class="h-14 bg-dominant border-b border-slate-700 px-6 flex items-center justify-between flex-shrink-0">
                {{-- Global Search --}}
                <div class="relative w-96">
                    @include('components.global-search')
                </div>

                {{-- Right: shift status + user --}}
                <div class="flex items-center gap-4">
                    @include('components.shift-status')
                    @auth
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="text-sm text-slate-400 hover:text-slate-100">Logout</button>
                    </form>
                    @endauth
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-auto p-6 bg-dominant">
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-400/10 border border-green-400/30 rounded-lg text-green-400 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-400/10 border border-red-400/30 rounded-lg text-red-400 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        // Tutup modal kalau klik backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                e.target.classList.add('hidden');
                e.target.classList.remove('flex');
            }
        });

        function handleGlobalSearch(query) {
            // TODO: Implement AJAX global search
        }
    </script>
    @stack('scripts')
</body>
</html>
