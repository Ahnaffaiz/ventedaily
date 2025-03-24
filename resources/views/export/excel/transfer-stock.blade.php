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
            <td style="font-size:16px; font-weight:500;">TRANSFER PRODUK KE {{ $transferTo == 'store' ? 'TOKO' : 'RUMAH' }}</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Tanggal : {{ $date }}</td>
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
        @foreach ($keepProducts as $keepProduct)
            <tr>
                <td width="20px"></td>
                <td style="border: 1px solid black; text-align: center;margin:20px; padding: 20px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black">{{ $keepProduct->productStock->product->name }}</td>
                <td style="border: 1px solid black">{{ $keepProduct->productStock->color->name }}</td>
                <td style="border: 1px solid black">{{ $keepProduct->productStock->size->name }}</td>
                @if ($transferTo == 'store')
                    <td style="border: 1px solid black; text-align: center;">{{ $keepProduct->home_stock }}</td>
                @elseif ($transferTo == 'home')
                    <td style="border: 1px solid black; text-align: center;">{{ $keepProduct->store_stock }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
