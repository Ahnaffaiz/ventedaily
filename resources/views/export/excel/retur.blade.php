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
            <td style="font-size:16px; font-weight:500;">RETUR REPORT</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
    </thead>
</table>
<table border="1" width="100%" cellpadding="4" cellspacing="4" style="border-collapse: collapse; font-size: 12px;">
    <thead>
        <tr>
            <th></th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">ID</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Sale ID</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tanggal</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Customer</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tot.Pch</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($returs as $retur)
            <tr>
                <td></td>
                <td style="border: 1px solid black; text-align:center;">{{ $retur->no_retur }}</td>
                <td style="border: 1px solid black; text-align:center;">{{ $retur->sale->no_sale }}</td>
                <td style="border: 1px solid black;width:150px;">{{ date('d-m-Y', strtotime($retur->created_at))}}</td>
                <td style="border: 1px solid black;width:120px;">{{ $retur->sale->customer->name }}</td>
                @php
                    $discount = $retur->discount_type === App\Enums\DiscountType::PERSEN ? $retur->sub_total * (int) $retur->discount / 100 : $retur->discount;
                @endphp
                <td style="border: 1px solid black; width:100px; text-align: right;"> Rp. {{ number_format($retur->total_price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; width:100px;">{{ $retur->user->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" style="border: 1px solid black"></td>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nama Produk</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Jumlah</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Harga</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Total</th>
            </tr>
            @foreach ($retur->returItems as $returItem)
            <tr>
                <td></td>
                <td colspan="2" style="border: 1px solid black"></td>
                <td style="border: 1px solid black">{{ $returItem->productStock->product->name }} {{ $returItem->productStock->color->name }} {{ $returItem->productStock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $returItem->total_items }}</td>
                <td style="border: 1px solid black; text-align: right;"> Rp. {{ number_format($returItem->price, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; text-align: right;"> Rp. {{ number_format($returItem->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td colspan="6" style="border: 1px solid black"></td>
            </tr>
        @endforeach

    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <th colspan="4" style="border: 1px solid black; text-align: center;"> Total </th>
            <th style="border:1px solid black; text-align: right;">  Rp. {{ number_format($total_price, 0, ',', '.') }}</th>
            <th style="border: 1px solid black"></th>
        </tr>
    </tfoot>

</table>
