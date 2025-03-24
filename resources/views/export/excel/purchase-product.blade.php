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
                <td style="border: 1px solid black; text-align: center;margin:20px; padding: 20px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black">{{ $product['product_name'] }}</td>
                <td style="border: 1px solid black">{{ $product['color'] }}</td>
                <td style="border: 1px solid black">{{ $product['size'] }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $product['qty'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
