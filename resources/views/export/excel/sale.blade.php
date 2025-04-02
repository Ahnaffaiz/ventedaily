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
            <td style="font-size:16px; font-weight:500;">SALE REPORT {{ $customer ? 'BY CUSTOMER' : ''}}</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
        @if ($group)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Grup : {{ $group->name }}</td>
            </tr>
        @endif
        @if ($customer)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Customer : {{ $customer->name }}</td>
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
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Customer</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Term</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tax</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Discount</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Ship</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tot.Sale</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Net.Sale</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Payment</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Sisa</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td></td>
                <td style="border: 1px solid black; text-align:center;">{{ $sale->no_sale }}</td>
                <td style="border: 1px solid black;width:100px;">{{ date('d-m-Y', strtotime($sale->created_at))}}</td>
                <td style="border: 1px solid black;width:100px;">{{ $sale->customer->name }}</td>
                <td style="border: 1px solid black; text-align: center; width:200px;"> {{ $sale->termOfPayment->name }}</td>
                @php
                    $discount = $sale->discount_type === App\Enums\DiscountType::PERSEN ? $sale->sub_total * (int) $sale->discount / 100 : $sale->discount;
                @endphp
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($sale->tax / 100 * ($sale->sub_total - $discount), 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($discount, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($sale->ship, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($sale->sub_total, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($sale->payment, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($sale->outstand_payment, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px;">{{ $sale->user->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="border: 1px solid black"></td>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nama Produk</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Jumlah</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Harga</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Total</th>
                <td colspan="5" style="border: 1px solid black"></td>
            </tr>
            @foreach ($sale->saleItems as $saleItem)
            <tr>
                <td></td>
                <td colspan="3" style="border: 1px solid black"></td>
                <td style="border: 1px solid black">{{ $saleItem->productStock->product->name }} {{ $saleItem->productStock->color->name }} {{ $saleItem->productStock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $saleItem->total_items }}</td>
                <td style="border: 1px solid black; text-align: right;"> Rp. {{ number_format($saleItem->price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; text-align: right;"> Rp. {{ number_format($saleItem->total_price, 0, ',', '.') }}</td>
                <td colspan="5" style="border: 1px solid black"></td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td colspan="12" style="border: 1px solid black"></td>
            </tr>
        @endforeach

    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <th colspan="4" style="border: 1px solid black; text-align: center;"> Total </th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_tax, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_discount, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_ship, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($sub_total, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_price, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_payment, 0, ',', '.') }}</th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_out_balance, 0, ',', '.') }}</th>
            <th style="border: 1px solid black"></th>
        </tr>
    </tfoot>

</table>
