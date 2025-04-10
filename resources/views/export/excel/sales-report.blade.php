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
            <td style="font-size:16px; font-weight:500;">SALES REPORT ACCOUNTING</td>
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
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tanggal</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Customer</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Total Items</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Sales</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Discount</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tax</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Net Sales</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">HPP</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Profit</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Payment</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Amount Payment</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Marketplace Price</th>
            @foreach ($banks as $bank)
                <th style="border: 1px solid black; text-align:center; font-weight: bold;">{{ $bank->name }}</th>
            @endforeach
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Cash</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">WD Marketplace</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">WD Date</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Tiktok Fee</th>
            <th style="border: 1px solid black; text-align:center; font-weight: bold;">Shopee Fee</th>
        </tr>
    </thead>
    <tbody>
        @if ($sales)
        @foreach ($sales as $sale)
            <tr>
                <td></td>
                <td style="border: 1px solid black;">{{ $sale['no_sale'] }}</td>
                <td style="border: 1px solid black;width:100px;">{{ date('d-m-Y', strtotime($sale['date']))}}</td>
                <td style="border: 1px solid black;width:100px;">{{ ucwords($sale['customer']) }}</td>
                <td style="border: 1px solid black;width:100px;">{{ $sale['total_items'] }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['sub_total'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['discount'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['tax'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['total_price'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['hpp'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['profit'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">{{ $sale['payment_type'] }}</td>
                <td style="border: 1px solid black;width:120px;">Rp. {{ number_format($sale['payment_amount'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:120px;">Rp. {{ number_format($sale['marketplace_price'], 0, ',', '.') }}</td>
                @foreach ($banks as $bank)
                    <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale[$bank->name], 0, ',', '.') }}</td>
                @endforeach
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['cash'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['wd_amount'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">{{ $sale['wd_date'] ? date('d-m-Y', strtotime($sale['wd_date'])) : ''}}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['tiktok_fee'], 0, ',', '.') }}</td>
                <td style="border: 1px solid black;width:100px;">Rp. {{ number_format($sale['shopee_fee'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
        @endif
    </tbody>

</table>
