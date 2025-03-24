<table>
    <thead>
        <tr></tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 14px; font-weight: 500;">{{ $setting->name }}</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">{{ $setting->address }}</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size:16px; font-weight:500;">LAPORAN PEMBELIAN PRODUK</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} sampai {{ $end_date }}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th width="20px"></th>
            <th style="border: 1px solid black; text-align:center">No</th>
            <th style="border: 1px solid black; text-align:center">Term</th>
            <th style="border: 1px solid black; text-align:center">Amount</th>
            <th style="border: 1px solid black; text-align:center">Discount</th>
            <th style="border: 1px solid black; text-align:center">Tax</th>
            <th style="border: 1px solid black; text-align:center">Ship</th>
            <th style="border: 1px solid black; text-align:center">Total Purchase</th>
            <th style="border: 1px solid black; text-align:center">Outs Balance</th>
            <th style="border: 1px solid black; text-align:center">Date</th>
            <th style="border: 1px solid black; text-align:center">Created By</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchases as $purchase)
            <tr>
                <td width="20px"></td>
                <td style="border: 1px solid black; text-align: center;margin:20px; padding: 20px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black">{{ $purchase->termOfPayment->name }}</td>
                <td style="border: 1px solid black; width: 100px;">Rp. {{number_format($purchase->sub_total, 0, ',', '.') }}</td>
                @php
                    $discount = $purchase->discount_type === App\Enums\DiscountType::PERSEN ? $purchase->sub_total * (int) $purchase->discount / 100 : $purchase->discount;
                @endphp
                <td style="border: 1px solid black; width: 100px;">Rp. {{ number_format($discount, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width: 100px;">Rp. {{ number_format($purchase->tax / 100 * ($purchase->sub_total - $discount), 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width: 100px;">Rp. {{ number_format($purchase->ship, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width: 100px;">Rp. {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width: 100px;">Rp. {{ number_format($purchase->outstanding_balance, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width: 120px;">Rp. {{ $purchase->created_at->format('d/m/Y') }}</td>
                <td style="border: 1px solid black; width: 120px;">{{ $purchase->user->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
