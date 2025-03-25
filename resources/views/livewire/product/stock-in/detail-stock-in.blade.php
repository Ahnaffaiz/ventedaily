<div class="overflow-x-auto">
    <div class="p-6 border">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th scope="col" class="px-4 py-2 text-sm font-medium text-center text-gray-500">No</th>
                    <th scope="col" class="px-4 py-2 text-sm font-medium text-gray-500 text-start">Name</th>
                    <th scope="col" class="px-4 py-2 text-sm font-medium text-gray-500 text-start">Color</th>
                    <th scope="col" class="px-4 py-2 text-sm font-medium text-gray-500 text-start">Size</th>
                    <th scope="col" class="px-4 py-2 text-sm font-medium text-gray-500 text-start">Qty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @if ($stockIn?->stockInProducts)
                    @foreach ($stockIn?->stockInProducts as $stockInProduct)
                        <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                            <td class="px-4 py-2 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $stockInProduct->productStock->product->name }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $stockInProduct->productStock->color->name }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $stockInProduct->productStock->size->name }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $stockInProduct->stock }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
