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
            <td style="font-size:16px; font-weight:500;">EXPENSE REPORT</td>
        </tr>
        <tr>
            <td width="20px"></td>
            <td style="font-size: 12px;">Periode : {{ $start_date }} s.d {{ $end_date }}</td>
        </tr>
        @if ($cost)
            <tr>
                <td width="20px"></td>
                <td style="font-size: 12px;">Grup : {{ $cost->name }}</td>
            </tr>
        @endif
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th width="20px"></th>
            <th style="border: 1px solid black; text-align:center">No</th>
            <th style="border: 1px solid black; text-align:center">Date</th>
            <th style="border: 1px solid black; text-align:center">Name</th>
            <th style="border: 1px solid black; text-align:center">Description</th>
            <th style="border: 1px solid black; text-align:center">Amount</th>
            <th style="border: 1px solid black; text-align:center">Qty</th>
            <th style="border: 1px solid black; text-align:center">Uom</th>
            <th style="border: 1px solid black; text-align:center">Total Expense</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expenses as $expense)
            <tr>
                <td width="20px"></td>
                <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; width: 100px;">{{ $expense->date }}</td>
                <td style="border: 1px solid black; width: 150px;">{{ $expense->cost->name }}</td>
                <td style="border: 1px solid black; width: 120px;">{{ $expense->desc }}</td>
                <td style="border: 1px solid black; width: 80px; text-align: end;">Rp. {{ number_format($expense->amount, '0', ',' ,'.') }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $expense->qty }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $expense->uom }}</td>
                <td style="border: 1px solid black; width: 100px; text-align: end;">Rp. {{ number_format($expense->total_amount, '0', ',' ,'.') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td width="20px"></td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;" colspan="7">Total</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: end;">Rp. {{ number_format($expenses->sum('total_amount'), '0', ',' ,'.') }}</td>
        </tr>
    </tfoot>
</table>
