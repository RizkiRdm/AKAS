@props(['type' => 'button'])
{{-- Reference: DESIGN.md --}}
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'bg-accent text-slate-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-cyan-300 focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-dominant']) }}>
    {{ $slot }}
</button>
