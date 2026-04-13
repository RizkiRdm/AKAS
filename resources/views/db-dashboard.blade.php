<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Dashboard</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto space-y-8">
        <h1 class="text-3xl font-bold text-gray-800">📊 Database & Migration Status Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Connection Status -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold mb-4">🔗 Connection Status</h2>
                @if($connection)
                    <div class="flex items-center text-green-600 font-medium">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        Connected to PostgreSQL
                    </div>
                @else
                    <div class="flex flex-col text-red-600 font-medium">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            Connection Failed
                        </div>
                        <p class="text-sm mt-2 font-mono bg-red-50 p-2 rounded">{{ $error }}</p>
                    </div>
                @endif
            </div>

            <!-- Performance Test -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold mb-4">⚡ Performance Test (CRUD)</h2>
                @if($perfTest)
                    <div class="text-2xl font-bold {{ $perfTest < 200 ? 'text-green-600' : 'text-orange-600' }}">
                        {{ number_format($perfTest, 2) }} ms
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Goal: < 200ms</p>
                @else
                    <div class="text-gray-400">Not tested yet</div>
                @endif
            </div>
        </div>

        <!-- Table Counts -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold mb-4">📁 Table Records Count</h2>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
                @foreach($counts as $table => $count)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="text-xs uppercase text-gray-500 font-bold mb-1">{{ str_replace('_', ' ', $table) }}</div>
                        <div class="text-xl font-bold text-blue-600">{{ is_numeric($count) ? number_format($count) : 'Error' }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Migration Status -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold mb-4">📜 Migration Status (Artisan)</h2>
            <pre class="bg-gray-900 text-gray-200 p-4 rounded-lg text-xs overflow-auto max-h-64 leading-relaxed">{{ $migrationStatus }}</pre>
        </div>
        
        <div class="text-center text-gray-400 text-sm">
            Powered by Laravel Telescope & PHP 8.5
        </div>
    </div>
</body>
</html>
