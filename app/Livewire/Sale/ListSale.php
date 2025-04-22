<?php

namespace App\Livewire\Sale;

use App\Enums\DiscountType;
use App\Enums\KeepStatus;
use App\Enums\PaymentType;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Exports\SaleByProductExport;
use App\Exports\SaleExport;
use App\Exports\SaleProductExport;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Keep;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\Printer;

class ListSale extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $sale;
    public $isOpen = false, $isPayment = false, $isExport = false;
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc', $groupIds, $groupId;

    #[Rule('required')]
    public $start_date, $end_date, $exportType = 'product';
    public $customer_id, $customers, $group_id, $groups, $product_id, $products;

    public $total_price, $sub_total_after_discount;

    public $filter = 'today';

    public $query_filter_start, $query_filter_end;
    public $filters = [
        'today' => 'Today',
        'this_week' => 'This Week',
        'this_month' => 'This Month',
        'this_year' => 'This Year',
        'all' => 'All',
    ];

    public $btraw;

    public $showColumns = [
        'keep_id' => true,
        'pre_order_id' => true,
        'order_id_marketplace' => true,
        'group' => true,
        'term_of_payment_id' => true,
        'total_items' => true,
        'sub_total' => true,
        'discount' => true,
        'tax' => false,
        'total_price' => true,
        'payment_type' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    protected $listeners = [
        'delete'
    ];

    #[Title('Sale')]

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $column;
    }

    public function updatedShowColumns($column)
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
        if ($this->filter === 'today') {
            $this->query_filter_start = Carbon::today();
            $this->query_filter_end = Carbon::today();
        } elseif ($this->filter === 'this_week') {
            $this->query_filter_start = Carbon::now()->startOfWeek();
            $this->query_filter_end = Carbon::now()->endOfWeek();
        } elseif ($this->filter === 'this_month') {
            $this->query_filter_start = Carbon::now()->startOfMonth();
            $this->query_filter_end = Carbon::now()->endOfMonth();
        } elseif ($this->filter === 'this_year') {
            $this->query_filter_start = Carbon::now()->startOfYear();
            $this->query_filter_end = Carbon::now()->endOfYear();
        } else {
            $this->query_filter_start = Carbon::parse('1970-01-01');
            $this->query_filter_end = Carbon::today();
        }
    }

    public function mount()
    {
        $this->query_filter_start = Carbon::today();
        $this->query_filter_end = Carbon::today();
        $this->groupIds = Group::get();
    }
    public function render()
    {
        return view('livewire.sale.list-sale', [
            'sales' => Sale::select('sales.*')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->leftJoin('keeps', 'sales.keep_id', '=', 'keeps.id')
                ->leftJoin('pre_orders', 'sales.pre_order_id', '=', 'pre_orders.id')
                ->leftJoin('sale_payments', 'sales.id', '=', 'sale_payments.sale_id')
                ->leftJoin('banks', 'sale_payments.bank_id', '=', 'banks.id')
                ->where('customers.group_id', 'like', '%' . $this->groupId . '%')
                ->where(function($query) {
                    $query->where('sales.no_sale', 'like', '%' . $this->query . '%')
                        ->orWhere('customers.name', 'like', '%' . $this->query . '%')
                        ->orWhere('keeps.no_keep', 'like', '%' . $this->query . '%')
                        ->orWhere('pre_orders.no_pre_order', 'like', '%' . $this->query . '%')
                        ->orWhere('sales.order_id_marketplace', 'like', '%' . $this->query . '%')
                        ->orWhere('customers.group_id', 'like', '%' . $this->query . '%')
                        ->orWhere('sales.total_items', 'like', '%' . $this->query . '%')
                        ->orWhere('sales.total_price', 'like', '%' . $this->query . '%')
                        ->orWhere('sale_payments.payment_type', 'like', '%' . $this->query . '%')
                        ->orWhere('banks.name', 'like', '%' . $this->query . '%');
                })
                ->whereBetween('sales.created_at', [
                    Carbon::parse($this->query_filter_start)->startOfDay(),
                    Carbon::parse($this->query_filter_end)->endOfDay(),
                ])
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function updatedGroupId()
    {
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
    }

    public function addPayment($sale)
    {
        $this->isPayment = true;
        $this->sale = Sale::with('salePayment')->where('id', $sale)->first();
        $this->isOpen = true;
    }

    public function show($sale_id) {
        $this->isOpen = true;
        $this->isPayment = false;
        $this->isExport = false;
        $this->sale = Sale::find($sale_id);
        $this->getTotalPrice();
    }

    public function getTotalPrice()
    {
        $this->total_price = $this->sale->sub_total;
        if(strtolower($this->sale->discount_type) === strtolower(DiscountType::PERSEN)) {
            $this->sub_total_after_discount = $this->sale->sub_total - round($this->sale->sub_total* (int) $this->sale->discount/100);
            $this->total_price = $this->sub_total_after_discount;
        } elseif(strtolower($this->sale->discount_type) === strtolower(DiscountType::RUPIAH)) {
            $this->sub_total_after_discount = $this->sale->sub_total - $this->sale->discount;
            $this->total_price = $this->sub_total_after_discount;
        } else {
            $this->sub_total_after_discount = $this->total_price;
        }
        if($this->sale->tax) {
            $this->total_price = $this->sub_total_after_discount + round($this->sub_total_after_discount* (int) $this->sale->tax/100);
        }
        if($this->sale->ship) {
            $this->total_price = $this->total_price + $this->sale->ship;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function deleteAlert($sale)
    {
        $this->sale = Sale::find($sale);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete Sale ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'delete',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function delete()
    {
        $stockStype = $this->sale->customer->group_id == 1 ? 'store_stock' : 'home_stock';
        try {
            foreach ($this->sale->saleItems as $saleItem) {
                $saleItem->productStock->update([
                    'all_stock' => $saleItem->productStock->all_stock + $saleItem->total_items,
                    $stockStype => $saleItem->productStock->$stockStype + $saleItem->total_items,
                ]);
                setStockHistory(
                    $saleItem->productStock->id,
                    StockActivity::SALES,
                    StockStatus::REMOVE,
                    $stockStype,
                    NULL,
                    $saleItem->total_items,
                    $this->sale->no_sale,
                    $saleItem->productStock->all_stock,
                    $saleItem->productStock->home_stock,
                    $saleItem->productStock->store_stock,
                    $saleItem->productStock->pre_order_stock,
                );
            }
            if($this->sale->Keep()->exists()){
                $this->sale->keep->update([
                    'status' => KeepStatus::CANCELED
                ]);
            }

            $this->sale->delete();
            $this->alert('success', 'Sale Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function openModalExport()
    {
        $this->customers = Customer::all()->pluck('name', 'id')->toArray();
        $this->groups = Group::all()->pluck('name', 'id')->toArray();
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        $this->isExport = true;
        $this->isOpen = true;
    }

    public function searchCustomer($query)
    {
        $this->customers = Customer::all()->pluck('name', 'id')->toArray();
        if ($query) {
            $this->customers = Customer::where('name', 'like', '%'.$query.'%')
            ->where('group_id', $this->group_id)
            ->pluck('name', 'id')
            ->toArray();
        }
    }

    public function searchProduct($query)
    {
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        if ($query) {
            $this->products = collect(Product::all()->pluck('name', 'id')->toArray())
                ->filter(function ($label, $value) use ($query) {
                    return stripos($label, $query) !== false;
                })
                ->toArray();
            }
    }

    public function exportExcel()
    {
        if($this->exportType == 'product') {
            $this->validate();
            $name = "Data Penjualan Product Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            return Excel::download(new SaleProductExport($this->start_date, $this->end_date, $this->group_id, $this->customer_id), $name);
        } elseif($this->exportType == 'sale') {
            $this->validate();
            $name = "Data Penjualan Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            return Excel::download(new SaleExport($this->start_date, $this->end_date, $this->group_id, $this->customer_id), $name);
        } elseif($this->exportType == "by_product") {
            $this->validate();
            $product = null;
            if($this->product_id) {
                $product = Product::where('id', $this->product_id)->first();
            }
            $name = "Data Penjualan Produk " . $product?->name . ' ' . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            return Excel::download(new SaleByProductExport($this->start_date, $this->end_date, $this->product_id), $name);
        }
        $this->start_date = null;
        $this->end_date = null;
        $this->exportType = 'product';
        $this->isExport = false;
    }

    public function printInvoice($saleId)
    {
        // Ambil data utama penjualan
        $sale = Sale::where('id', $saleId)->first();

        // Ambil pengaturan perusahaan
        $setting = Setting::first();
        $discount = 0;
        $shipping = 0;
        if(strtolower($sale->discount_type) === strtolower(DiscountType::PERSEN)) {
            $discount = round($sale->sub_total* (int) $sale->discount/100);
        } elseif(strtolower($sale->discount_type) === strtolower(DiscountType::RUPIAH)) {
            $discount = $sale->discount;
        }

        $ship = $sale->ship ? $sale->ship : 0;
        $sub_total = $sale->sub_total - ($discount + $ship);

        $tax = 0;
        if($sale->tax) {
            $tax = round($sub_total * (int) $sale->tax/100);
        }

        // Konfigurasi printer
        $connector = new DummyPrintConnector();
        $profile = CapabilityProfile::load("simple");
        $printer = new Printer($connector, $profile);

        // Fungsi pembantu untuk mencetak 3 kolom
        $buatBaris4Kolom = function ($kolom1, $kolom2, $kolom3) {
            $lebar_kolom_1 = 8;
            $lebar_kolom_2 = 10;
            $lebar_kolom_3 = 10;

            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);

            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);

            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));
            $hasilBaris = [];

            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
                $hasilKolom1 = str_pad($kolom1Array[$i] ?? "", $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad($kolom2Array[$i] ?? "", $lebar_kolom_2, " ", STR_PAD_LEFT);
                $hasilKolom3 = str_pad($kolom3Array[$i] ?? "", $lebar_kolom_3, " ", STR_PAD_LEFT);
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
            }

            return implode("\n", $hasilBaris) . "\n";
        };

        // Fungsi pembantu 1 kolom
        $buatBaris1 = fn($kolom1) => wordwrap($kolom1, 33, "\n", true);

        // HEADER
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text($setting->name . "\n");
        $printer->selectPrintMode();
        $printer->text("Beteng Trade Center \n");
        $printer->text("Lantai 1 Blok A4  no 14. \n");
        $printer->text("Phone : 0813-9302-4717 \n");
        $printer->text("--------------------------------\n");

        // Info nota
        $printer->initialize();
        $printer->text("NO    : INV-" . Carbon::parse($sale->created_at)->format('d-F-Y') . "-" . $sale->no_sale . "\n");
        $printer->text("Time  : " . Carbon::parse($sale->created_at)->format('d-F-Y') . "\n");
        $printer->text("--------------------------------\n");

        // Customer
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("*" . $sale->customer->name . "*\n");
        $printer->text("Nomor Keep : " . $sale->Keep?->no_keep . "*\n");
        $printer->feed();

        // Detail barang
        $printer->initialize();
        foreach ($sale->saleItems as $saleItem) {
            $printer->text($buatBaris1( ucwords($saleItem->productStock->product->name) . " " . ucwords($saleItem->productStock->color->name) . " " . ucwords($saleItem->productStock->size->name) . "\n"));
            $printer->text($buatBaris4Kolom($saleItem->total_items . "x", number_format($saleItem->price), number_format($saleItem->total_price)));
        }

        // Ringkasan pembayaran
        $printer->text("--------------------------------\n");
        $printer->text($buatBaris4Kolom('', "SubTotal", number_format($sale->sub_total)));
        $printer->text($buatBaris4Kolom('', "Diskon", number_format($discount)));
        $printer->text($buatBaris4Kolom('', "Pengiriman", number_format($ship)));
        $printer->text($buatBaris4Kolom('', "Pajak", number_format($tax)));
        $printer->text("--------------------------------\n");
        $printer->text($buatBaris4Kolom('', "TOTAL :", number_format($sale->total_price)));
        $printer->text($buatBaris4Kolom('', "Total pcs :", number_format($sale->total_items)));
        $printer->text($buatBaris4Kolom('', "TUNAI :", number_format($sale->salePayment?->cash_received)));
        $printer->text($buatBaris4Kolom('', "Kembali :", number_format($sale->salePayment?->cash_change * -1)));

        // Info tambahan
        $printer->feed();
        $printer->initialize();
        $printer->text("Pembayaran :" . strtolower($sale->salePayment->payment_type) == strtolower(PaymentType::TRANSFER) ? ucwords($sale->salePayment->bank->name) : 'Cash'  . "\n");
        $printer->text("Kasir : Admin Vente\n");

        // Footer
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("*Terimakasih*\n");
        $printer->initialize();
        $printer->selectPrintMode(Printer::MODE_UNDERLINE);
        $printer->text("Periksa Barang Anda Terlebih Dahulu, Penukaran Barang Max 3x24 Jam*\n\n");
        $printer->text("Dapatkan cashback Rp. 5.000,- untuk setiap pembelian kelipatan 10pcs. Batas penukaran nota maksimal 1 bulan dari tanggal transaksi.* \n");
        $printer->cut();

        // Output hasil cetakan ke browser
        $data = $connector->getData();
        $print = base64_encode($data);
        $btraw = 'rawbt:base64,' . $print;
        $this->dispatch('print-rawbt',  $btraw);
    }

}
