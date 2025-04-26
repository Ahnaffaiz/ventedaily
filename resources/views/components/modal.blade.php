@props(['title' => 'Modal Title', 'isOpen' => false, 'closeButton' => null, 'saveButton' => null, 'large' => false, 'saveLabel' => 'save'])

<div x-data="{ open: @entangle($attributes->wire('model')).live }" x-show="open"
    class="fixed inset-0 z-50 overflow-y-auto"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-show="open"></div>
        <div class="relative w-full {{ $large ? 'max-w-7xl' : 'max-w-2xl' }} p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" wire:click="{{ $closeButton }}">
                    <span class="sr-only">Close</span>
                    <i class="text-xl ri-close-line"></i>
                </button>
            </div>

            <div class="overflow-y-auto">
                {{ $slot }}
            </div>

            <div class="flex justify-end mt-6 gap-x-4">
                <button class="bg-gray-100 btn" wire:click="{{ $closeButton }}" type="button">
                    Cancel
                </button>
                @if ($saveButton)
                    <button wire:click="{{ $saveButton }}" class="text-white btn bg-primary" wire:target="{{ $saveButton }}" wire:loading.attr="disabled">
                        <div class="flex gap-2" wire:loading.remove wire:target="{{ $saveButton }}">
                            {{ $saveLabel }}
                        </div>
                        <div class="flex gap-2" wire:loading wire:target="{{ $saveButton }}">
                            <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                        </div>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
