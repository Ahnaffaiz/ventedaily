<div class="p-4 border rounded-md dark:border-gray-600">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <tbody>
            <tr class="bg-gray-50 dark:bg-gray-800">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Date</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">{{ $sale?->saleWithdrawal?->date }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-800">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Amount</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">Rp {{ number_format($sale?->saleWithdrawal?->amount, 0,',', '.') }}</td>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-800">
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200">Marketplace Price</td>
                <td class="px-2 py-2 text-md text-gray-950 dark:text-gray-200 text-end">:</td>
                <td class="px-2 py-2 font-semibold text-md text-gray-950 dark:text-gray-200">Rp {{ number_format($sale?->saleWithdrawal?->marketplace_price, 0,',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
