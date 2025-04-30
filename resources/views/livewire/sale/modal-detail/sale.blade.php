<div class="p-4 mb-2 border rounded-md dark:border-gray-600">
    <table class="min-w-full">
        <tbody>
            <tr>
                <td class="font-normal text-md text-start">No Sale:</td>
                <td class="font-normal text-md text-start">Type:</td>
                <td class="font-normal text-md text-start">Customer Name:</td>
                <td class="font-normal text-md text-start">Term Of Payment:</td>
            </tr>
            <tr>
                <td class="font-bold text-md text-start">{{ $sale?->no_sale }}</td>
                <td class="font-bold text-md text-start">{{ $sale?->customer?->group?->name }}</td>
                <td class="font-bold text-md text-start">{{ $sale?->customer?->name }}</td>
                <td class="font-bold text-md text-start">{{ $sale?->termOfPayment->name }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="p-6 border border-gray-200 rounded-md dark:border-gray-600">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
            <tr>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-center text-gray-500 dark:text-gray-200">No</th>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Name</th>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Color</th>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Size</th>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Price</th>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Qty</th>
                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Total Price</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @if ($sale?->saleItems)
                @foreach ($sale?->saleItems as $saleItem)
                    <tr class="">
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $saleItem->productStock->product->name }}
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $saleItem->productStock->color->name }}
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $saleItem->productStock->size->name }}
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            Rp. {{ number_format($saleItem->price, 0, ',', '.') }}
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $saleItem->total_items }}
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            Rp. {{ number_format($saleItem->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="py-2 text-end"></td>
                    <td class="py-2 text-end">
                        <p class="mt-2 mb-2 text-lg font-semibold">Sub Total :</p>
                        <div class="mb-2">
                            <span class="text-base font-bold text-success dark:text-success">
                                Discount
                            </span>
                            @if (strtolower($sale->discount_type) === App\Enums\DiscountType::PERSEN)
                                <span
                                    class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-success/10 text-success dark:bg-success/50 dark:text-white">{{ $sale->discount }}%</span>:
                            @endif
                        </div>
                        <div class="mb-2">
                            <span class="text-base font-bold text-danger dark:text-danger">
                                Tax
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-danger/10 text-danger dark:bg-danger/50 dark:text-white">{{ $sale->tax }}%</span>:
                        </div>
                        <div class="mb-2">
                            <span class="text-base font-bold">
                                Shipping Cost :
                            </span>
                        </div>
                    </td>
                    <td class="py-2 font-semibold text-md text-start ps-4">
                        <p class="mt-2 text-lg font-semibold text-end">Rp
                            {{ number_format($sale->sub_total, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-base font-semibold text-success dark:text-success text-end">
                            -Rp.
                            {{ strtolower($sale->discount_type) === App\Enums\DiscountType::PERSEN ? number_format($sale->sub_total * (int) $sale->discount / 100, 0, ',', '.') : number_format((int) $sale->discount, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-base font-semibold text-danger dark:text-danger text-end">
                            +Rp. {{ number_format($sub_total_after_discount * (int) $sale->tax / 100, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-base font-semibold text-end">
                            +Rp. {{ number_format($sale->ship, 0, ',', '.') }}
                        </p>
                    </td>
                </tr>
                <tr class="border-none">
                    <td colspan="5" class="py-2 text-end"></td>
                    <td class="py-2 text-xl font-bold text-end">Total Price:</td>
                    <td class="py-2 text-xl font-bold text-end"> Rp.
                        {{ number_format($sale->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
