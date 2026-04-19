{{-- Ref: DESIGN.md & ARCHITECTURE.md --}}
@php
    $activeShift = auth()->user()?->activeShift;
@endphp

<a href="{{ route('shift.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 transition-colors group">
    <div class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-green-400 shadow-[0_0_8px_rgba(74,222,128,0.5)]' : 'bg-red-400 shadow-[0_0_8px_rgba(248,113,113,0.5)]' }}"></div>
    <span class="text-xs font-medium text-slate-300 group-hover:text-slate-100">
        {{ $activeShift ? 'Shift Aktif' : 'Buka Shift' }}
    </span>
</a>
