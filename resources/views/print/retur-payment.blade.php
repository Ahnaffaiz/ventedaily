<!DOCTYPE html>
<html>
<head>
	<title>Retur</title>
</head>
<body style="font-family: courier; font-size: 12px;width: 100%;">
	<p><span style="font-size:16px;"><strong>{{ $setting->company_name }}</strong></span><br>
		<span>{{ $setting->address }}
	</p></span>
	<hr>
	<H3>NOTA RETUR<br>No. RTR-{{date('Y')}}-{{date('m')}}-{{$retur->id}}</H3>
	<table style="margin-top: -5px;width: 80%">
		<tr>
			<td colspan="3">
				<hr>
			</td>
		</tr>

		<?php if(!empty($retur->returItems)) {?>
		@foreach($retur->returItems as $returItem)
		<tr style="font-size: 11px;">
			<td collspan="3">{{ $returItem->productStock->product->name }} {{ $returItem->productStock->color->name }} - {{ $returItem->productStock->size->name }}</td>
		</tr>
		<tr>
			<td style="text-align: right;">[{{ number_format($returItem->total_items) }}]</td>
			<td style="text-align: right;">{{ number_format($returItem->price) }}</td>
		</tr>
		@endforeach
		<?php }?>
		<tr>
			<td colspan="3">
				<hr>
			</td>
		</tr>
		<tr style="font-size: 12px;">
			<td colspan="2" style="text-align: left;">Nilai Retur (Rp.)</td>
			<td style="text-align: center;">{{ number_format($retur->total_price) }}</td>
		</tr>
	    <tr>
			<td colspan="3">
				<hr>
			</td>
		</tr>
		<tr style="font-size: 12px;margin-top: 5px">
			<td colspan="4" style="text-align: left;">{{ date('d-M-Y H:i:s') }}</td>
		</tr>
		<tr style="font-size: 12px;">
			<td colspan="4" style="text-align: left;">Diterima Oleh</td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr><td>............</td></tr>
		<tr>
			<td colspan="4" style="text-align: left;">{{ Auth::user()->name }}</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align: left;">Ket : {{ $retur->desc }}</td>
		</tr>
		<tr style="font-size: 10px;">
			<td colspan="4" style="text-align: center;"> * Terima Kasih *</td>
		</tr>
	</table>
</body>
</html>
