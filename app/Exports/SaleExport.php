<?php

namespace App\Exports;

use App\Enums\DiscountType;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SaleExport implements FromView
{
    protected $start_date, $end_date, $sales, $customer;
    protected $sub_total, $total_price, $total_ship, $total_tax, $total_discount, $total_out_balance, $total_payment;
    public function __construct($start_date, $end_date, $supplier_id = null)
    {
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        if ($supplier_id) {
            $this->customer = Supplier::where('id', $supplier_id)->first();
            $this->sales = Purchase::where('supplier_id', $this->customer->id)
                ->whereBetween('created_at', [$start_date, $end_date])->get();

        } else {
            $this->sales = Purchase::whereBetween('created_at', [$start_date, $end_date])->get();
        }

        $this->sub_total = $this->sales->sum('sub_total');
        $this->total_price = $this->sales->sum('total_price');
        $this->total_ship = $this->sales->sum('ship');
        $this->total_out_balance = $this->sales->sum('outstanding_balance');
        foreach ($this->sales as $sale) {
            $discount = $sale->discount_type === DiscountType::PERSEN ? $sale->sub_total * (int) $sale->discount / 100 : $sale->discount;
            $tax = $sale->tax / 100 * ($sale->sub_total - $discount);
            $payment = $sale->salePayment->amount;
            $this->total_discount += $discount;
            $this->total_tax += $tax;
            $this->total_payment += $payment;
        }
    }

    public function view() : View
    {
        return view('export.excel.sale', [
            'sales' => $this->sales,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
            'customer' => $this->customer,
            'sub_total' => $this->sub_total,
            'total_price' => $this->total_price,
            'total_discount' => $this->total_discount,
            'total_ship' => $this->total_ship,
            'total_tax' => $this->total_tax,
            'total_payment' => $this->total_payment,
            'total_out_balance' => $this->total_out_balance,
        ]);
    }
}
