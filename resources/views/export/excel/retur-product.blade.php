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
            <td style="font-size:16px; font-weight:500;">LAPORAN RETUR PRODUK</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th width="20px"></th>
            <th style="border: 1px solid black; text-align:center">No</th>
            <th style="border: 1px solid black; text-align:center">Date</th>
            <th style="border: 1px solid black; text-align:center">Name</th>
            <th style="border: 1px solid black; text-align:center">Color</th>
            <th style="border: 1px solid black; text-align:center">Size</th>
            <th style="border: 1px solid black; text-align:center">Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td width="20px"></td>
                <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; width: 100px;">{{ $product['date'] }}</td>
                <td style="border: 1px solid black; width: 150px;">{{ $product['product_name'] }}</td>
                <td style="border: 1px solid black; width: 120px;">{{ $product['color'] }}</td>
                <td style="border: 1px solid black; width: 80px;">{{ $product['size'] }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $product['qty'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td width="20px"></td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;" colspan="5">Total</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{ $returs->sum('total_items') }}</td>
        </tr>
    </tfoot>
</table>
