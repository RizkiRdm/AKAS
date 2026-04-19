{{-- Ref: DESIGN.md & ARCHITECTURE.md --}}
@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-slate-100">Shift Reconciliation</h2>
    @if(!$activeShift)
        <button onclick="openModal('modal-start-shift')" class="bg-accent text-slate-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-cyan-300">
            Buka Shift Baru
        </button>
    @else
        <button onclick="openModal('modal-end-shift')" class="bg-red-500/10 text-red-400 border border-red-500/30 px-4 py-2 rounded-lg text-sm hover:bg-red-500/20">
            Tutup Shift Aktif
        </button>
    @endif
</div>

@if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-400/10 border border-green-400/30 rounded-lg text-green-400 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 px-4 py-3 bg-red-400/10 border border-red-400/30 rounded-lg text-red-400 text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 gap-6">
    {{-- Active Shift Card --}}
    @if($activeShift)
    <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 shadow-lg">
        <h3 class="text-lg font-medium text-slate-100 mb-4">Shift Aktif ({{ $activeShift->user->name }})</h3>
        <div class="grid grid-cols-3 gap-6">
            <div>
                <span class="block text-sm text-slate-400 mb-1">Kas Awal</span>
                <span class="text-xl font-bold text-slate-100">Rp {{ number_format($activeShift->starting_float) }}</span>
            </div>
            <div>
                <span class="block text-sm text-slate-400 mb-1">Waktu Buka</span>
                <span class="text-slate-100">{{ $activeShift->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex items-end">
                <span class="px-2 py-1 bg-green-400/10 text-green-400 rounded text-xs font-bold uppercase tracking-wider">Open</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Shift History --}}
    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-lg">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700 bg-slate-900/50">
                    <th class="px-4 py-3 text-left text-slate-400 font-medium">Waktu</th>
                    <th class="px-4 py-3 text-left text-slate-400 font-medium">Kasir</th>
                    <th class="px-4 py-3 text-right text-slate-400 font-medium">Kas Awal</th>
                    <th class="px-4 py-3 text-right text-slate-400 font-medium">Expected</th>
                    <th class="px-4 py-3 text-right text-slate-400 font-medium">Ending</th>
                    <th class="px-4 py-3 text-right text-slate-400 font-medium">Variance</th>
                    <th class="px-4 py-3 text-center text-slate-400 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($shifts as $shift)
                <tr class="hover:bg-slate-700/50 transition-colors">
                    <td class="px-4 py-3 text-slate-100">
                        {{ $shift->created_at->format('d/m/y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-slate-300">{{ $shift->user->name }}</td>
                    <td class="px-4 py-3 text-right text-slate-300">Rp {{ number_format($shift->starting_float) }}</td>
                    <td class="px-4 py-3 text-right text-slate-300">
                        {{ $shift->status === 'closed' ? 'Rp '.number_format($shift->expected_cash) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right text-slate-300">
                        {{ $shift->status === 'closed' ? 'Rp '.number_format($shift->ending_cash) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($shift->status === 'closed')
                            <span class="{{ $shift->variance == 0 ? 'text-green-400' : 'text-red-400' }} font-medium">
                                Rp {{ number_format($shift->variance) }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tighter {{ $shift->status === 'open' ? 'bg-green-400/10 text-green-400' : 'bg-slate-700 text-slate-400' }}">
                            {{ $shift->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500 italic">Belum ada riwayat shift.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($shifts->hasPages())
        <div class="p-4 border-t border-slate-700 bg-slate-900/50">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modals --}}
<x-modal id="modal-start-shift" title="Buka Shift Baru">
    <form action="{{ route('shift.start') }}" method="POST">
        @csrf
        <x-form-input label="Kas Awal (Starting Float)" name="starting_float" type="number" placeholder="0" required />
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="closeModal('modal-start-shift')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700 hover:text-slate-100 transition-colors">Batal</button>
            <button type="submit" class="bg-accent text-slate-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-cyan-300 transition-colors">Buka Shift</button>
        </div>
    </form>
</x-modal>

@if($activeShift)
<x-modal id="modal-end-shift" title="Tutup Shift Aktif">
    <form action="{{ route('shift.end', $activeShift) }}" method="POST">
        @csrf
        <div class="mb-6 p-4 bg-yellow-400/10 border border-yellow-400/30 rounded-lg">
            <p class="text-sm text-yellow-400">
                <strong>Blind Count:</strong> Masukkan jumlah kas yang ada di laci saat ini. Sistem akan menghitung selisih secara otomatis.
            </p>
        </div>
        <x-form-input label="Total Kas Akhir (Ending Cash)" name="ending_cash" type="number" placeholder="0" required />
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="closeModal('modal-end-shift')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700 hover:text-slate-100 transition-colors">Batal</button>
            <button type="submit" class="bg-red-500 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition-colors">Tutup & Rekonsiliasi</button>
        </div>
    </form>
</x-modal>
@endif

@endsection
