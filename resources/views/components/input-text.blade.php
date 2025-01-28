@props(['id', 'name', 'title', 'placeholder' => null])

<div class="mb-3">
    <label class="mb-2" for="{{ $id }}">{{ $title }}</label>
    <input type="text" id="{{ $id }}" class="form-input {{ $errors->first($name) ? 'border border-red-500' : '' }}"
        wire:model="{{ $name }}">
    @error($name)
        <span class="font-normal text-danger text-small">{{ $message }}</span>
    @enderror
</div>
