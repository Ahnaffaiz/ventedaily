<div class="overflow-x-auto">
    <div class="p-6 mb-2 border">
        <table class="min-w-full">
            <tbody>
                <tr>
                    <td class="font-normal text-md text-start">No Pre Order:</td>
                    <td class="font-normal text-md text-start">Type:</td>
                    <td class="font-normal text-md text-start">Customer Name:</td>
                    <td class="font-normal text-md text-start">End Pre Order:</td>
                </tr>
                <tr>
                    <td class="font-bold text-md text-start">{{ $preOrder?->no_pre_order }}</td>
                    <td class="font-bold text-md text-start">{{ $preOrder?->customer?->group?->name }}</td>
                    <td class="font-bold text-md text-start">{{ $preOrder?->customer?->name }}</td>
                    <td class="font-bold text-md text-start">{{ $preOrder?->pre_order_time }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="p-6 border">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Name</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Qty</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total Price</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @if ($preOrder?->preOrderProducts)
                    @foreach ($preOrder?->preOrderProducts as $preOrderProduct)
                        <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $preOrderProduct->productStock->product->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $preOrderProduct->productStock->color->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $preOrderProduct->productStock->size->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                Rp. {{ number_format($preOrderProduct->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $preOrderProduct->total_items }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                Rp. {{ number_format($preOrderProduct->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="border-none">
                        <td colspan="5" class="py-4 text-end"></td>
                        <td class="py-4 text-xl font-bold text-end">Total Price:</td>
                        <td class="py-4 text-xl font-bold text-end"> Rp.
                            {{ number_format($total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
