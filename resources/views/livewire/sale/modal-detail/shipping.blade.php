<div class="border">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <tbody>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Total Price</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">Rp {{ number_format($sale?->total_price, 0,',', '.') }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Sale Date</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->created_at }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Customer Name</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->customer_name }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Phone</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->phone ? '+62' . $sale?->saleShipping?->phone : '' }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Marketplace</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->marketplace?->name }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Shipping Status</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">
                    @if ($sale?->saleShipping?->status)
                        @if ($sale?->saleShipping?->status == strtolower(App\Enums\ShippingStatus::SIAPKIRIM))
                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">
                                {{ ucfirst($sale?->saleShipping?->status) }}
                            </span>
                        @elseif ($sale?->saleShipping?->status == strtolower(App\Enums\ShippingStatus::EKSPEDISI))
                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-warning/10 text-warning">
                                {{ ucfirst($sale?->saleShipping?->status) }}
                            </span>
                        @elseif ($sale?->saleShipping?->status == strtolower(App\Enums\ShippingStatus::SELESAI))
                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-success/10 text-success">
                                {{ ucfirst($sale?->saleShipping?->status) }}
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-secondary/10 text-secondary">
                            Cashier
                        </span>
                    @endif
                </td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Shipping Date</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->date }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">No Resi</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->no_resi }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">ID Order</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->order_id_marketplace }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">City</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->city }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-900">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Address</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleShipping?->address }}</td>
            </tr>
        </tbody>
    </table>
</div>
