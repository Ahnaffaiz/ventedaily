@props(['paginator', 'pageName' => null])

<div class="flex items-center space-x-2">
    @if($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Previous
                    </button>
                @endif

                {{-- First Page --}}
                @if($paginator->currentPage() > 3)
                    <button wire:click="gotoPage(1, '{{ $pageName }}')" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        1
                    </button>
                    <span class="px-3 py-1 text-sm font-medium text-gray-500">...</span>
                @endif

                {{-- Pagination Elements --}}
                @for($i = max(1, $paginator->currentPage() - 1); $i <= min($paginator->lastPage(), $paginator->currentPage() + 1); $i++)
                    @if ($i == $paginator->currentPage())
                        <span class="px-3 py-1 text-sm font-medium text-white border rounded-md bg-primary border-primary">
                            {{ $i }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $i }}, '{{ $pageName }}')" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            {{ $i }}
                        </button>
                    @endif
                @endfor

                {{-- Last Page --}}
                @if($paginator->currentPage() < $paginator->lastPage() - 2)
                    <span class="px-3 py-1 text-sm font-medium text-gray-500">...</span>
                    <button wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $pageName }}')" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ $paginator->lastPage() }}
                    </button>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Next
                    </button>
                @else
                    <span class="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                        Next
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
