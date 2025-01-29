@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-500 bg-white border border-gray-300 rounded-md cursor-default dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                    {!! __('Previous') !!}
                </span>
            @else
                <button wire:click="previousPage"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-blue-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring focus:ring-blue-500 dark:text-blue-400 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700">
                    {!! __('Previous') !!}
                </button>
            @endif

            <div class="flex items-center">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="mx-1 text-gray-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="inline-flex items-center px-4 py-2 mx-1 text-sm font-medium leading-5 text-white bg-blue-600 border border-blue-600 rounded-md cursor-default">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})"
                                    class="inline-flex items-center px-4 py-2 mx-1 text-sm font-medium leading-5 text-blue-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring focus:ring-blue-500 dark:text-blue-400 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-blue-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring focus:ring-blue-500 dark:text-blue-400 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700">
                    {!! __('Next') !!}
                </button>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-500 bg-white border border-gray-300 rounded-md cursor-default dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                    {!! __('Next') !!}
                </span>
            @endif
        </div>
    </nav>
@endif
