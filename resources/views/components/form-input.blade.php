@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => '', 'required' => false, 'id' => null])
{{-- Reference: DESIGN.md --}}
<div class="mb-4">
    <label for="{{ $id ?? $name }}" class="mb-1 block text-sm font-medium text-slate-300">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $id ?? $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        class="focus:border-accent focus:ring-accent/20 {{ isset($errors) && $errors->has($name) ? 'border-red-500' : '' }} w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2"
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $attributes }}>
    @isset($errors)
        @error($name)
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    @endisset
</div>
