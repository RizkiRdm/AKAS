@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => '', 'required' => false])
{{-- Reference: DESIGN.md --}}
<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-300 mb-1">{{ $label }}</label>
    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/20 placeholder-slate-500 {{ isset($errors) && $errors->has($name) ? 'border-red-500' : '' }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
    @isset($errors)
        @error($name)
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    @endisset
</div>
