@props(['id', 'title'])
{{-- Reference: DESIGN.md --}}
<div id="{{ $id }}" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/70">
    <div class="max-h-[90vh] w-[520px] overflow-y-auto rounded-2xl border border-slate-700 bg-slate-800 shadow-2xl">
        {{-- Modal header --}}
        <div class="flex items-center justify-between border-b border-slate-700 px-6 py-4">
            <h3 class="text-lg font-medium text-slate-100">{{ $title }}</h3>
            <button onclick="closeModal('{{ $id }}')" class="text-slate-400 hover:text-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        {{-- Modal body --}}
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
