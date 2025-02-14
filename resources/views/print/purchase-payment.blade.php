<!DOCTYPE html>
<html>

<head>
    <title>NOTA PEMBAYARAN</title>
</head>

<body style="font-family: courier; font-size: 12px;width: 100%;">
    <p><span style="font-size:16px;"><strong>{{ $setting->name }}</strong></span><br>
        <span>{{ $setting->address }}
    </p></span>
    <H3>NOTA PEMBAYARAN KE SUPPLIER<br>No. PDB-{{date('Y')}}-{{date('m')}}-{{$payment->id}}</H3>
    <table style="margin-top: -5px;width: 80%">
        <tr>
            <td>Supplier</td>
            <td>:</td>
            <td colspan="2">[ {{ $payment->purchase->supplier->id}} ] {{$payment->purchase->supplier->name}}</td>
        </tr>

        <tr>
            <td>Term</td>
            <td>:</td>
            <td colspan="2">{{ $payment->purchase->term_of_payment }} Hari </td>
        </tr>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;">Nilai Pembelian (Rp.)</td>
            <td style="text-align: right;">{{ number_format($payment->purchase->total_price) }}</td>
        </tr>
        <tr>
            <td colspan="4">..................................</td>
        </tr>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;"><strong>Total Pembayaran (Rp.)</strong></td>
            <td style="text-align: right;font-size: 15px;">
                <strong>{{ number_format($payment->amount) }}</strong>
            </td>
        </tr>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;">Cara Pembayaran</td>
            <td style="text-align: right;">{{ $payment->payment_type }}</td>
        </tr>
        <?php if (strtolower($payment->payment_type) == 'transfer') {?>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;">No Rek.</td>
            <td style="text-align: right;">{{ $payment->account_number }}</td>
        </tr>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;">Nama Pemilik</td>
            <td style="text-align: right;">{{ $payment->account_name }}</td>
        </tr>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;">Bank</td>
            <td style="text-align: right;">{{ $payment->bank->name }}</td>
        </tr>
        <?php }?>
        <tr style="font-size: 12px;">
            <td colspan="3" style="text-align: left;"><strong>Sisa (Rp.)</strong></td>
            <td style="text-align: right;"><strong>{{ number_format($payment->purchase->outstanding_balance) }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="4">..................................</td>
        </tr>

        <tr style="font-size: 11px;">
            <td colspan="2" style="text-align: left;">Tanggal</td>
            <td colspan="2" style="text-align: right;">{{ date('d-m-Y H:i:s', strtotime($payment->date)) }}
            </td>
        </tr>
        <tr style="font-size: 12px;">
            <td colspan="4" style="text-align: left;">Diterima Oleh</td>

        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td>............</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: left;">{{ $payment->purchase->supplier->name }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: left;">Ket : {{ $payment->desc }}</td>
        </tr>
        <tr style="font-size: 10px;">
            <td colspan="4" style="text-align: center;"> * Terima Kasih *</td>
        </tr>
    </table>
</body>

</html>
