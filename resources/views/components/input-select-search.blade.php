@props(['id', 'name', 'title', 'placeholder' => null, 'options' => [], 'searchFunction' => 'search', 'selectedLabel' => null])

<div
    x-data="{
        open: false,
        search: '',
        selectedOption: '{{ old($name, $attributes->wire('model')->value()) }}',
        searchFunction: '{{ $searchFunction }}'
    }"
    x-init="$watch('search', value => $wire.call(searchFunction, value))"
    x-effect="search = @js($selectedLabel)"
    class="mt-3"
>
    <label class="block mb-2 dark:text-dark-primary" for="{{ $id }}">{{ $title }}</label>

    <div class="relative">
        <input type="text"
            x-model="search"
            @focus="open = true"
            @click.away="open = false"
            @input="$wire.call(searchFunction, search)"
            class="dark:text-gray-200 w-full form-input {{ $errors->first($name) ? 'border-2 border-danger dark:border-danger' : '' }} "
            placeholder="{{ $placeholder ?? 'Search...' }}"
        >

        <div x-show="open"
            class="absolute z-50 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-28 dark:bg-slate-700 dark:border-dark-border">

            @if (empty($options))
                <div class="px-4 py-2 text-gray-500 dark:text-dark-muted">No results found</div>
            @else
                @foreach ($options as $value => $label)
                    <div class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-dark-tertiary dark:text-dark-secondary"
                        @click="
                            selectedOption = '{{ $value }}';
                            search = '{{ $label }}';
                            open = false;
                            $wire.set('{{ $name }}', '{{ $value }}')
                        ">
                        {{ $label }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <input type="hidden" wire:model="{{ $name }}" id="{{ $id }}">

    @error($name)
        <span class="font-normal is-invalid text-danger dark:text-danger text-small" id="is-invalid">{{ $message }}</span>
    @enderror
</div>
