@props(['title' => 'Modal Title', 'isOpen' => false, 'closeButton' => null, 'saveButton' => null, 'large' => false])

<div x-data="{ open: @entangle($attributes->wire('model')) }" x-show="open"
    class="fixed top-0 left-0 z-50 w-full h-full overflow-y-auto transition-all duration-500 bg-gray-800 bg-opacity-50"
    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div
        class="flex flex-col m-3 transition-all duration-300 ease-in-out bg-white rounded shadow-sm {{ $large ? 'sm:max-w-7xl' : 'sm:max-w-2xl'}} sm:w-full sm:mx-auto dark:bg-gray-800">
        <div class="flex justify-between items-center py-2.5 px-4 border-b dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-600 dark:text-white">
                {{ $title }}
            </h3>
            <button class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 dark:text-gray-200"
                wire:click="{{ $closeButton }}" type="button">
                <i class="text-2xl ri-close-line"></i>
            </button>
        </div>
        <div class="p-4 overflow-y-auto">
            {{ $slot }}
        </div>
        <div class="flex items-center justify-end gap-2 p-4 border-t dark:border-slate-700">
            <button class="text-gray-800 transition-all btn bg-light" wire:click="{{ $closeButton }}" type="button">
                Close
            </button>
            @if ($saveButton)
                <button class="text-white btn bg-primary" wire:click="{{ $saveButton }}">Save</button>
            @endif
        </div>
    </div>
</div>
