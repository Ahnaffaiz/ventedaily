@props(['id', 'name', 'title', 'placeholder' => null, 'options' => [], 'searchFunction' => 'search'])

<div x-data="{
        open: false,
        search: '',
        selectedOption: null,
        searchFunction: '{{ $searchFunction }}'
    }" x-init="$watch('search', value => $wire.call(searchFunction, value))" class="mt-3">

    <label class="block mb-2" for="{{ $id }}">{{ $title }}</label>

    <div class="relative">
        <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
            @input="$wire.call(searchFunction, search)"
            class="w-full form-input {{ $errors->first($name) ? 'border-2 border-danger' : '' }}"
            placeholder="{{ $placeholder ?? 'Search...' }}">

        <div x-show="open" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
            @if (empty($options))
                <div class="px-4 py-2 text-gray-500">No results found</div>
            @else
                @foreach ($options as $value => $label)
                    <div class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                        @click="selectedOption = '{{ $value }}'; search = '{{ $label }}'; open = false; $wire.set('{{ $name }}', '{{ $value }}')">
                        {{ $label }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <input type="hidden" wire:model="{{ $name }}" id="{{ $id }}">

    @error($name)
        <span class="font-normal is-invalid text-danger text-small" id="is-invalid">{{ $message }}</span>
    @enderror
</div>
