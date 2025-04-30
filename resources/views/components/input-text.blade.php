@props(['id', 'name', 'title', 'placeholder' => null, 'type' => 'text', 'prepend' => null, 'disabled' => false, 'accept' => null])

<div>
    <label class="mt-3 mb-2 dark:text-dark-primary" for="{{ $id }}">{{ $title }}</label>
    @if($prepend)
        <div class="flex">
            <span
                class="inline-flex items-center px-4 text-sm text-gray-500 border border-r-0 border-gray-200 rounded-l min-w-fit bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">{{$prepend}}</span>
            <input type="{{ $type }}" id="{{ $id }}" @if ($disabled) disabled @endif
                class="form-input rounded-l-none {{ $errors->first($name) ? 'border-2 border-danger dark:border-rose-500' : '' }} dark:text-gray-200"
                wire:model.live="{{ $name }}" placeholder="{{ $placeholder }}" accept="{{ $accept }}">
        </div>
    @else
        <input type="{{ $type }}" id="{{ $id }}" @if ($disabled) disabled @endif
            class="form-input {{ $errors->first($name) ? 'border-2 border-danger dark:border-rose-500' : '' }}" wire:model.live="{{ $name }}"
            placeholder="{{ $placeholder }}" accept="{{ $accept }}">
    @endif
    @if ($type == 'file')
        <span class="font-normal is-invalid text-small" wire:loading wire:target={{ $name }}>Uploading..</span>
    @endif
    @error($name)
        <span class="font-normal is-invalid text-danger dark:text-danger text-small" id="is-invalid">{{ $message }}</span>
    @enderror
</div>
