@props(['paginator', 'perPageOptions', 'perPageProperty' => 'perPage', 'pageName' => 'page'])

<div class="px-3 py-4">
    <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center">
                <label for="{{ $perPageProperty }}" class="mr-2 text-sm text-gray-600">Show</label>
                <select id="{{ $perPageProperty }}" wire:model.live="{{ $perPageProperty }}" class="form-input">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                <span class="ml-2 text-sm text-gray-600">entries</span>
            </div>

            <div class="text-sm text-gray-600">
                Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries
            </div>
        </div>
        <div>
            <x-pagination :paginator="$paginator" :pageName="$pageName" />
        </div>
    </div>
</div>
