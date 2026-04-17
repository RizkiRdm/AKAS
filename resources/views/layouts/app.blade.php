<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AKAS POS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-900 text-slate-100 antialiased">
    {{-- layouts/app.blade.php --}}
    <div class="flex h-screen overflow-hidden bg-slate-900">

        {{-- SIDEBAR (fixed 240px) --}}
        <aside class="flex w-60 flex-shrink-0 flex-col border-r border-slate-700 bg-slate-800">
            {{-- Logo --}}
            <div class="flex h-14 items-center border-b border-slate-700 px-5">
                <span class="text-accent text-lg font-bold tracking-tight">AKAS POS</span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 px-3 py-4">
                <a href="/dashboard"
                    class="{{ request()->is('dashboard') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Dashboard
                </a>
                <a href="/master-stok"
                    class="{{ request()->is('master-stok*') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Master Stok
                </a>
                <a href="/sales"
                    class="{{ request()->is('sales*') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Transaksi
                </a>
                <a href="/shift"
                    class="{{ request()->is('shift*') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Shift
                </a>
                <a href="/reports"
                    class="{{ request()->is('reports*') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Laporan
                </a>
                <a href="/audit"
                    class="{{ request()->is('audit*') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Audit Log
                </a>
                @can('manage-users')
                <a href="/users"
                    class="{{ request()->is('users*') ? 'bg-slate-700 text-slate-100' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-700 hover:text-slate-100">
                    Manajemen User
                </a>
                @endcan
            </nav>

            {{-- User info bottom --}}
            <div class="border-t border-slate-700 p-4">
                <span class="text-sm text-slate-400">{{ auth()->user()->nama_pegawai ?? 'Guest' }}</span>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="flex min-w-0 flex-1 flex-col">

            {{-- HEADER --}}
            <header
                class="flex h-14 flex-shrink-0 items-center justify-between border-b border-slate-700 bg-slate-900 px-6">
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
            <main class="flex-1 overflow-auto bg-slate-900 p-6">
                @if (session('success'))
                    <div
                        class="mb-4 rounded-lg border border-green-400/30 bg-green-400/10 px-4 py-3 text-sm text-green-400">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm text-red-400">
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
