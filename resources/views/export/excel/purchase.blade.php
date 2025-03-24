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
        <tr>
            <td width="20px"></td>
            <td style="font-size:16px; font-weight:500;">PURCHASE REPORT {{ $supplier ? 'BY SUPPLYER' : ''}}</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
        @if ($supplier)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Supplier : {{ $supplier->name }}</td>
            </tr>
        @endif
    </thead>
</table>
<table border="1" width="100%" cellpadding="4" cellspacing="4" style="border-collapse: collapse; font-size: 12px;">
    <thead>
        <tr>
            <th></th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">ID</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tanggal</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Supplier</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Term</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tax</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Discount</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tot.Pch</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Net.Pch</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Payment</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Sisa</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchases as $purchase)
            <tr>
                <td></td>
                <td style="border: 1px solid black; text-align:center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black;width:100px;">{{ date('d-m-Y', strtotime($purchase->created_at))}}</td>
                <td style="border: 1px solid black;width:100px;">{{ $purchase->supplier->name }}</td>
                <td style="border: 1px solid black; text-align: center; width:200px;"> {{ $purchase->termOfPayment->name }}</td>
                @php
                    $discount = $purchase->discount_type === App\Enums\DiscountType::PERSEN ? $purchase->sub_total * (int) $purchase->discount / 100 : $purchase->discount;
                @endphp
                <td style="border: 1px solid black; width:100px; text-align: right;">Rp. {{ number_format($purchase->tax / 100 * ($purchase->sub_total - $discount), 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;">Rp. {{ number_format($discount, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;">Rp. {{ number_format($purchase->sub_total, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;">Rp. {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;">Rp. {{ number_format($purchase->payment, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;">Rp. {{ number_format($purchase->outstand_payment, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px;">{{ $purchase->user->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="border: 1px solid black"></td>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nama Produk</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Jumlah</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Harga</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Total</th>
                <td colspan="4" style="border: 1px solid black"></td>
            </tr>
            @foreach ($purchase->purchaseItems as $purchaseItem)
            <tr>
                <td></td>
                <td colspan="3" style="border: 1px solid black"></td>
                <td style="border: 1px solid black">{{ $purchaseItem->productStock->product->name }} {{ $purchaseItem->productStock->color->name }} {{ $purchaseItem->productStock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $purchaseItem->total_items }}</td>
                <td style="border: 1px solid black; text-align: right;">Rp. {{ number_format($purchaseItem->price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; text-align: right;">Rp. {{ number_format($purchaseItem->total_price, 0, ',', '.') }}</td>
                <td colspan="4" style="border: 1px solid black"></td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td colspan="11" style="border: 1px solid black"></td>
            </tr>
        @endforeach

    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <th colspan="4" style="border: 1px solid black; text-align: center;"> Total </th>
            <th style="border:1px solid black; text-align: right;"> Rp. {{ number_format($total_tax, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;"> Rp. {{ number_format($total_discount, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;"> Rp. {{ number_format($sub_total, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;"> Rp. {{ number_format($total_price, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;"> Rp. {{ number_format($total_payment, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;"> Rp. {{ number_format($total_out_balance, 0, ',', '.') }}</th>
            <th style="border: 1px solid black"></th>
        </tr>
    </tfoot>

</table>
