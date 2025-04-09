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
            <td style="font-size:16px; font-weight:500;">TRANSFER STOCK</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
        @if ($stockType)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Stock In : {{ ucwords(str_replace('_', ' ', $stockType)) }}</td>
            </tr>
        @endif
    </thead>
</table>
<table border="1" width="100%" cellpadding="4" cellspacing="4" style="border-collapse: collapse; font-size: 12px;">
    <thead>
        <tr>
            <th></th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">No</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tanggal</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Stock</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stockIns as $stockIn)
            <tr>
                <td></td>
                <td style="border: 1px solid black; text-align:center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black;width:100px;">{{ date('d-m-Y', strtotime($stockIn->created_at))}}</td>
                <td style="border: 1px solid black;width:200px;">{{ ucwords(str_replace('_', ' ', $stockIn->stock_type)) }}</td>
                <td style="border: 1px solid black; width:100px;">{{ $stockIn->user->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" style="border: 1px solid black"></td>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Nama Produk</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Jumlah</th>
            </tr>
            @foreach ($stockIn->stockInProducts as $stockInProduct)
            <tr>
                <td></td>
                <td colspan="2" style="border: 1px solid black"></td>
                <td style="border: 1px solid black">{{ $stockInProduct->productStock->product->name }} {{ $stockInProduct->productStock->color->name }} {{ $stockInProduct->productStock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $stockInProduct->stock }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td colspan="4" style="border: 1px solid black"></td>
            </tr>
        @endforeach

    </tbody>
</table>
