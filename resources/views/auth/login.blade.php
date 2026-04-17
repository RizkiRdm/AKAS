<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AKAS POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 antialiased">
    {{-- Reference: DESIGN.md & ARCHITECTURE.md --}}
    <div class="flex min-h-screen items-center justify-center p-6">
        <div class="w-full max-w-md space-y-8 rounded-2xl border border-slate-700 bg-slate-800 p-8 shadow-2xl">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-white">AKAS POS</h1>
                <p class="mt-2 text-sm text-slate-400">Silakan login untuk melanjutkan</p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('login.authenticate') }}" method="POST">
                @csrf
                <div class="space-y-4 rounded-md shadow-sm">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-300">Username</label>
                        <input id="username" name="username" type="text" required value="{{ old('username') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-white placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm"
                            placeholder="Masukkan username">
                        @error('username')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 block w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-white placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm"
                            placeholder="Masukkan password">
                        @error('password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        @error('error')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative flex w-full justify-center rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
