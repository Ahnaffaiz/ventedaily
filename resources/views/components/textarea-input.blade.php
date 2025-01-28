@props(['id', 'name', 'title', 'placeholder' => null, 'class' => null])

<div class="mb-3">
    <label class="mb-2" for="{{ $id }}">{{ $title }}</label>
    <textarea class="form-input {{ $errors->first($name) ? 'border border-red-500' : '' }}" id="{{ $id }}"
        wire:model="{{ $name }}" rows="5"></textarea>
    @error($name)
        <span class="font-normal text-danger text-small">{{ $message }}</span>
    @enderror
</div>
