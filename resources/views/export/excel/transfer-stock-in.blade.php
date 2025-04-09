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
        @if ($stockFrom)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Dari : {{ ucwords(str_replace('_', ' ', $stockFrom)) }}</td>
            </tr>
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Ke : {{ ucwords(str_replace('_', ' ', $stockTo)) }}</td>
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
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Dari</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Ke</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transferStocks as $transferStock)
            <tr>
                <td></td>
                <td style="border: 1px solid black; text-align:center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black;width:100px;">{{ date('d-m-Y', strtotime($transferStock->created_at))}}</td>
                <td style="border: 1px solid black;width:100px;">{{ ucwords(str_replace('_', ' ', $transferStock->transfer_from)) }}</td>
                <td style="border: 1px solid black;width:100px;">{{ ucwords(str_replace('_', ' ', $transferStock->transfer_from)) }}</td>
                <td style="border: 1px solid black; width:100px;">{{ $transferStock->user->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" style="border: 1px solid black"></td>
                <th colspan="2" style="border: 1px solid black; text-align: center; font-weight: bold;">Nama Produk</th>
                <th style="border: 1px solid black; text-align: center; font-weight: bold;">Jumlah</th>
            </tr>
            @foreach ($transferStock->transferProducts as $transferProductStock)
            <tr>
                <td></td>
                <td colspan="2" style="border: 1px solid black"></td>
                <td colspan="2" style="border: 1px solid black">{{ $transferProductStock->productStock->product->name }} {{ $transferProductStock->productStock->color->name }} {{ $transferProductStock->productStock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $transferProductStock->stock }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td colspan="5" style="border: 1px solid black"></td>
            </tr>
        @endforeach

    </tbody>
</table>
