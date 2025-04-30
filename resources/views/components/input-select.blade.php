@props(['id', 'name', 'title', 'placeholder' => null, 'options' => []])

<div class="mt-3">
    <label class="block mb-2 dark:text-dark-primary" for="{{ $id }}">{{ $title }}</label>
    <select class="dark:text-gray-200 w-full form-input {{ $errors->first($name) ? 'border-2 border-danger dark:border-rose-500' : '' }}"
        wire:model.change="{{ $name }}" id="{{ $id }}">
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    @error($name)
        <span class="font-normal is-invalid text-danger text-small dark:text-danger" id="is-invalid">{{ $message }}</span>
    @enderror
</div>
