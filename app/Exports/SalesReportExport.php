<?php

namespace App\Exports;

use App\Enums\DiscountType;
use App\Enums\PaymentType;
use App\Models\Bank;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class SalesReportExport implements FromView
{
    protected $sales, $start_date, $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $sales = Sale::whereBetween('created_at', [$start_date, $end_date])->get();
        foreach ($sales as $sale) {

            //discount
            if($sale->discount) {
                $discount = $sale->discount_type === DiscountType::PERSEN ? $sale->sub_total * (int) $sale->discount / 100 : $sale->discount;
            }

            //tax
            if($sale->tax) {
                $tax = $sale->tax / 100 * ($sale->sub_total - $discount);
            }

            //profit and hpp
            $hpp = DB::table('sale_items')
                        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                        ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                        ->where('sales.id', $sale->id)
                        ->select(DB::raw('SUM(product_stocks.purchase_price * sale_items.total_items) as hpp'))
                        ->value('hpp');
            $profit = $sale->total_price - $hpp;

            $this->sales[$sale->id]['no_sale'] = $sale->no_sale;
            $this->sales[$sale->id]['date'] = Carbon::parse($sale->created_at)->format('d-m-Y');
            $this->sales[$sale->id]['customer'] = $sale->customer->name;
            $this->sales[$sale->id]['total_items'] = $sale->total_items;
            $this->sales[$sale->id]['sub_total'] = $sale->sub_total;
            $this->sales[$sale->id]['discount'] = $discount;
            $this->sales[$sale->id]['tax'] = $tax;
            $this->sales[$sale->id]['total_price'] = $sale->total_price;
            $this->sales[$sale->id]['hpp'] = $hpp;
            $this->sales[$sale->id]['profit'] = $profit;
            $this->sales[$sale->id]['payment_type'] = $sale->salePayment->payment_type == PaymentType::TRANSFER ? $sale->salePayment->bank->name : $sale->salePayment->payment_type;
            $this->sales[$sale->id]['payment_amount'] = $sale->salePayment->amount;

            $banks = Bank::get();
            foreach ($banks as $bank) {
                if ($sale->salePayment->payment_type == PaymentType::TRANSFER) {
                    $this->sales[$sale->id][$bank->name] = $sale->salePayment->bank_id == $bank->id ? $sale->salePayment->amount : 0;
                    $this->sales[$sale->id]['cash'] = 0;
                } else {
                    $this->sales[$sale->id]['cash'] = $sale->salePayment->amount;
                    $this->sales[$sale->id][$bank->name] = 0;
                }
            }

            if($sale->saleWithdrawal) {
                $this->sales[$sale->id]['marketplace_price'] = $sale->saleWithdrawal->marketplace_price;
                $this->sales[$sale->id]['wd_amount'] = $sale->saleWithdrawal->withdrawal_amount;
                $this->sales[$sale->id]['wd_date'] = Carbon::parse($sale->saleWithdrawal->created_at)->format('d-m-Y');
                if($sale->saleShipping?->marketplace?->name == 'tiktok') {
                    $this->sales[$sale->id]['tiktok_fee'] = $sale->saleWithdrawal->marketplace_price - $sale->saleWithdrawal->withdrawal_amount;
                    $this->sales[$sale->id]['shopee_fee'] = 0;
                } elseif ($sale->saleShipping?->marketplace?->name == 'shopee') {
                    $this->sales[$sale->id]['shopee_fee'] = $sale->saleWithdrawal->marketplace_price - $sale->saleWithdrawal->withdrawal_amount;
                    $this->sales[$sale->id]['tiktok_fee'] = 0;
                } else {
                    $this->sales[$sale->id]['shopee_fee'] = 0;
                    $this->sales[$sale->id]['tiktok_fee'] = 0;
                }
            } else {
                $this->sales[$sale->id]['marketplace_price'] = 0;
                $this->sales[$sale->id]['wd_amount'] = 0;
                $this->sales[$sale->id]['wd_date'] = 0;
                $this->sales[$sale->id]['tiktok_fee'] = 0;
                $this->sales[$sale->id]['shopee_fee'] = 0;
            }
        }
    }
    public function view() : View
    {
        return view('export.excel.sales-report', [
            'sales' => $this->sales,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
            'banks' => Bank::get(),
        ]);
    }
}
