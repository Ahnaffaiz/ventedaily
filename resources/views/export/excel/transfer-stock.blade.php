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
            <td style="font-size:16px; font-weight:500;">TRANSFER PRODUK DARI {{ strtoupper(str_replace('_', ' ', $transferStock->transfer_from)) . " KE " . strtoupper(str_replace('_', ' ', $transferStock->transfer_to)) }}</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Tanggal : {{ \Carbon\Carbon::parse($transferStock->created_at)->format('d-m-Y') }}</td>
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
        @foreach ($transferStock->transferProducts as $transferProduct)
            <tr>
                <td width="20px"></td>
                <td style="border: 1px solid black; text-align: center;margin:20px; padding: 20px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black">{{ $transferProduct->productStock->product->name }}</td>
                <td style="border: 1px solid black">{{ $transferProduct->productStock->color->name }}</td>
                <td style="border: 1px solid black">{{ $transferProduct->productStock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $transferProduct->stock }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
