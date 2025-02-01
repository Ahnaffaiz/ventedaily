@props(['id', 'name', 'title'])

<div class="flex items-center mt-3">
    <input type="checkbox" class="form-switch text-success" id="{{ $id }}" wire:model="{{ $name }}">
    <label class="ms-1.5" for="{{ $id }}">{{ $title }}</label>
</div>
@error($name)
    <span class="font-normal text-danger text-small">{{ $message }}</span>
@enderror
