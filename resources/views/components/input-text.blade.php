@props(['id', 'name', 'title', 'placeholder' => null, 'type' => 'text', 'prepend' => null, 'disabled' => false])

<div>
    <label class="mt-3 mb-2" for="{{ $id }}">{{ $title }}</label>
    @if($prepend)
        <div class="flex">
            <span
                class="inline-flex items-center px-4 text-sm text-gray-500 border border-r-0 border-gray-200 rounded-l min-w-fit bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">{{$prepend}}</span>
            <input type="{{ $type }}" id="{{ $id }}" @if ($disabled) disabled @endif
                class="form-input rounded-l-none {{ $errors->first($name) ? 'border border-red-500' : '' }}"
                wire:model.live="{{ $name }}" placeholder="{{ $placeholder }}">
        </div>
    @else
        <input type="{{ $type }}" id="{{ $id }}" @if ($disabled) disabled @endif
            class="form-input {{ $errors->first($name) ? 'border border-red-500' : '' }}" wire:model.live="{{ $name }}"
            placeholder="{{ $placeholder }}">
    @endif
    @error($name)
        <span class="font-normal text-danger text-small">{{ $message }}</span>
    @enderror
</div>
