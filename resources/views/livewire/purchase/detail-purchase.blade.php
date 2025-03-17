<div class="overflow-x-auto">
    <div class="p-6 mb-2 border">
        <table class="min-w-full">
            <tbody>
                <tr>
                    <td class="font-normal text-md text-start">Supplier :</td>
                </tr>
                <tr>
                    <td class="font-bold text-md text-start">{{ $purchase?->supplier?->name }}</td>
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
                @if ($purchase?->purchaseItems)
                    @foreach ($purchase?->purchaseItems as $purchaseItem)
                        <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                            <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $purchaseItem->productStock->product->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $purchaseItem->productStock->color->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $purchaseItem->productStock->size->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                Rp. {{ number_format($purchaseItem->price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $purchaseItem->total_items }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                Rp. {{ number_format($purchaseItem->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="py-2 text-end"></td>
                        <td class="py-2 text-end">
                            <p class="mt-2 mb-2 text-lg font-semibold">Sub Total :</p>
                            <div class="mb-2">
                                <span class="text-base font-bold text-success">
                                    Discount
                                </span>
                                @if (strtolower($purchase->discount_type) === App\Enums\DiscountType::PERSEN)
                                    <span
                                        class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-success/10 text-success">{{ $purchase->discount }}%</span>:
                                @endif
                            </div>
                            <div class="mb-2">
                                <span class="text-base font-bold text-danger">
                                    Tax
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-danger/10 text-danger">{{ $purchase->tax }}%</span>:
                            </div>
                            <div class="mb-2">
                                <span class="text-base font-bold">
                                    Shipping Cost :
                                </span>
                            </div>
                        </td>
                        <td class="py-2 font-semibold text-md text-start ps-4">
                            <p class="mt-2 text-lg font-semibold text-end">Rp
                                {{ number_format($purchase->sub_total, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-base font-semibold text-success text-end">
                                -Rp.
                                {{ strtolower($purchase->discount_type) === App\Enums\DiscountType::PERSEN ? number_format($purchase->sub_total * (int) $purchase->discount / 100, 0, ',', '.') : number_format((int) $purchase->discount, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-base font-semibold text-danger text-end">
                                +Rp. {{ number_format($sub_total_after_discount * (int) $purchase->tax / 100, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-base font-semibold text-end">
                                +Rp. {{ number_format($purchase->ship, 0, ',', '.') }}
                            </p>
                        </td>
                    </tr>
                    <tr class="border-none">
                        <td colspan="5" class="py-2 text-end"></td>
                        <td class="py-2 text-xl font-bold text-end">Total Price:</td>
                        <td class="py-2 text-xl font-bold text-end"> Rp.
                            {{ number_format($purchase->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
