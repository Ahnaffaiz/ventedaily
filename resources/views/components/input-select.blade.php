@props(['id', 'name', 'title', 'placeholder' => null, 'options' => []])

<div class="mt-3">
    <label class="block mb-2" for="{{ $id }}">{{ $title }}</label>
    <select class="w-full form-input" wire:model="{{ $name }}" id="{{ $id }}">
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    @error($name)
        <span class="font-normal text-danger text-small">{{ $message }}</span>
    @enderror
</div>
