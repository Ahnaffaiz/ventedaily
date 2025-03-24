<?php

namespace App\Exports;

use App\Enums\KeepStatus;
use App\Models\KeepProduct;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class TransferStockExport implements FromView
{
    protected $transferTo, $keepProducts;

    public function __construct($transferTo)
    {
        $this->transferTo = $transferTo;
        if($this->transferTo == 'store') {
            $this->keepProducts = KeepProduct::whereHas('keep', function($query){
                return $query->where('status', strtolower(KeepStatus::ACTIVE))
                        ->whereHas('customer', function($query){
                            return $query->where('group_id', 1);
                        });
            })->where('home_stock', '!=', 0)
            ->get();
        } elseif($this->transferTo == 'home') {
            $this->keepProducts = KeepProduct::whereHas('keep', function($query){
                return $query->where('status', strtolower(KeepStatus::ACTIVE))
                        ->whereHas('customer', function($query){
                            return $query->where('group_id', 2);
                        });
            })->where('store_stock', '!=', 0)
            ->get();
        }

    }

    public function view(): View
    {
        return view('export.excel.transfer-stock', [
            'transferTo' => $this->transferTo,
            'keepProducts' => $this->keepProducts,
            'date' => Carbon::now()->format('d/m/Y'),
            'setting' => Setting::first()
        ]);
    }
}
