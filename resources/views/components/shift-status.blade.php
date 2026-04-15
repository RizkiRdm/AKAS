{{-- resources/views/components/shift-status.blade.php --}}
<div class="flex items-center gap-2">
    <div class="w-2 h-2 rounded-full {{ session('active_shift') ? 'bg-green-400' : 'bg-red-400' }}"></div>
    <span class="text-sm text-slate-300">{{ session('active_shift') ? 'Shift Aktif' : 'Shift Belum Dibuka' }}</span>
</div>
