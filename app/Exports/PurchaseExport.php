<?php

namespace App\Exports;

use App\Enums\DiscountType;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseExport implements FromView
{
    protected $start_date, $end_date, $purchases, $supplier;
    protected $sub_total, $total_price, $total_ship, $total_tax, $total_discount, $total_out_balance, $total_payment;
    public function __construct($start_date, $end_date, $supplier_id = null)
    {
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        if ($supplier_id) {
            $this->supplier = Supplier::where('id', $supplier_id)->first();
            $this->purchases = Purchase::where('supplier_id', $this->supplier->id)
                ->whereBetween('created_at', [$start_date, $end_date])->get();

        } else {
            $this->purchases = Purchase::whereBetween('created_at', [$start_date, $end_date])->get();
        }

        $this->sub_total = $this->purchases->sum('sub_total');
        $this->total_price = $this->purchases->sum('total_price');
        $this->total_ship = $this->purchases->sum('ship');
        $this->total_out_balance = $this->purchases->sum('outstanding_balance');
        foreach ($this->purchases as $purchase) {
            $discount = $purchase->discount_type === DiscountType::PERSEN ? $purchase->sub_total * (int) $purchase->discount / 100 : $purchase->discount;
            $tax = $purchase->tax / 100 * ($purchase->sub_total - $discount);
            $payment = $purchase->purchasePayments->sum('amount');
            $this->total_discount += $discount;
            $this->total_tax += $tax;
            $this->total_payment += $payment;
        }
    }

    public function view() : View
    {
        return view('export.excel.purchase', [
            'purchases' => $this->purchases,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
            'supplier' => $this->supplier,
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
