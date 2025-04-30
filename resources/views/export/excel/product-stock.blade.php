<table>
    <thead>
        <tr>
            <th style="border: 1px solid black; text-align:center">No</th>
            <th style="border: 1px solid black; text-align:center">Name</th>
            <th style="border: 1px solid black; text-align:center">Color</th>
            <th style="border: 1px solid black; text-align:center">Size</th>
            <th style="border: 1px solid black; text-align:center">Status</th>
            <th style="border: 1px solid black; text-align:center; width: 100px;">Purchase Price</th>
            <th style="border: 1px solid black; text-align:center; width: 100px;">Selling Price</th>
            <th style="border: 1px solid black; text-align:center; width: 80px;">Home Stock</th>
            <th style="border: 1px solid black; text-align:center; width: 80px;">Store Stock</th>
            <th style="border: 1px solid black; text-align:center; width: 100px;">Pre Order Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productStocks as $stock)
            <tr>
                <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; width: 150px;">{{ $stock->product->name }}</td>
                <td style="border: 1px solid black; width: 120px;">{{ $stock->color->name }}</td>
                <td style="border: 1px solid black; width: 80px;">{{ $stock->size->name }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $stock->status }}</td>
                <td style="border: 1px solid black; text-align: right;">{{ $stock->purchase_price }}</td>
                <td style="border: 1px solid black; text-align: right;">{{ $stock->selling_price }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $stock->home_stock }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $stock->store_stock }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $stock->pre_order_stock }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
