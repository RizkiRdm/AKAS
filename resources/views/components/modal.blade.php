@props(['id', 'title'])
{{-- Reference: DESIGN.md --}}
<div id="{{ $id }}" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-secondary rounded-2xl w-[520px] max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-700">
        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-medium text-slate-100">{{ $title }}</h3>
            <button onclick="closeModal('{{ $id }}')" class="text-slate-400 hover:text-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        {{-- Modal body --}}
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
