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
            <td style="font-size:16px; font-weight:500;">LAPORAN PEMBELIAN PRODUK {{ $product ? strtoupper($product->name) : '' }}</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
        @if ($product)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Product : {{ $product->name }}</td>
            </tr>
        @endif
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th width="20px"></th>
            <th style="border: 1px solid black; text-align:center">No</th>
            <th style="border: 1px solid black; text-align:center">Product Name</th>
            <th style="border: 1px solid black; text-align:center">Color</th>
            <th style="border: 1px solid black; text-align:center">Size</th>
            <th style="border: 1px solid black; text-align:center">Qty</th>
            <th style="border: 1px solid black; text-align:center">Total Pch</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td width="20px"></td>
                <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; width: 150px;">{{ $product['product_name'] }}</td>
                <td style="border: 1px solid black; width: 120px;">{{ $product['color'] }}</td>
                <td style="border: 1px solid black; width: 80px;">{{ $product['size'] }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $product['qty'] }}</td>
                <td style="border: 1px solid black; width: 100px;">Rp. {{ number_format($product['total_purchase'], '0', ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td width="20px"></td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;" colspan="4">Total</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{ $products->sum('qty') }}</td>
            <td style="border: 1px solid black; font-weight: bold;">Rp. {{ number_format($products->sum('total_purchase'), '0', ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
