<?php

namespace App\Livewire\Dashboard;

use App\Enums\DiscountType;
use App\Models\Bank;
use App\Models\Cost;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Keep;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;
    public $keep_items, $keep_prices;
    public $sale_today;
    public $total_sale, $total_btc, $total_vente, $total_tiktok, $total_shopee;
    public $cost, $discount;

    public $filter = 'week';
    public $filters;
    public $salesChartData = [];

    public $total_cash, $total_transfers = [];
    public $banks;

    public $sales, $profit, $purchase, $costs;
    #[Title('Dashboard')]

    public function mount()
    {
        $this->filters = [
            'week' => 'Week',
            'month' => 'Month',
            'year' => 'Year'
        ];

        $this->user = Auth::user();

        $this->updatedSalesChartData();
        $this->banks = Bank::all();
    }

    public function updatedFilter()
    {
        $this->updatedSalesChartData();
    }
    public function render()
    {
        $this->getResumeKeepSale();
        $this->getTotalPayment();
        $this->getDiscount();
        $this->getProfit();
        $this->cost = Expense::whereDate('created_at',  now())->sum('total_amount');
        return view('livewire.dashboard.dashboard');
    }

    public function getDiscount()
    {
        $this->discount = Sale::whereDate('created_at', Carbon::now())
            ->get()
            ->sum(function ($sale) {
                if ($sale->discount_type === DiscountType::PERSEN) {
                    return $sale->sub_total * ($sale->discount / 100);
                } elseif ($sale->discount_type === DiscountType::RUPIAH) {
                    return $sale->discount;
                } else {
                    return 0;
                }
            });
    }

    public function getProfit()
    {
        $this->sale_today['price'] = Sale::whereDate('created_at', Carbon::now())->get()->sum('total_price');
        $this->sale_today['items'] = Sale::whereDate('created_at', Carbon::now())->get()->sum('total_items');
        $totalHpp = Sale::whereDate('sales.created_at', Carbon::now())
                            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->select(DB::raw('SUM(product_stocks.purchase_price * sale_items.total_items) as hpp'))
                            ->value('hpp');
        $this->profit = $this->sale_today['price'] - $totalHpp;
    }

    public function getTotalPayment()
    {
        $salePayment = new SalePayment();
        $this->total_cash = $salePayment->getTotalCash();
        foreach ($this->banks as $bank) {
            $this->total_transfers[$bank->id] = [
                'bank' => $bank->name,
                'total' => $salePayment->getTotalTransfer($bank->id),
            ];
        }
    }

    public function getResumeKeepSale()
    {
        $this->keep_items = Keep::allTotalItems();
        $this->keep_prices = Keep::allTotalPrice();
    }

    public function updatedSalesChartData()
    {
        $labels = [];
        $data = [];
        $resellerData = [];
        $shopeeData = [];
        $tiktokData = [];
        $venteData = [];
        $shopee = Customer::where('name' ,'like', '%' . 'Shopee' .'%')->get()->pluck('id');
        $tiktok = Customer::where('name' ,'like', '%' . 'Tiktok' .'%')->get()->pluck('id');
        $vente = Customer::where('name' ,'like', '%' . 'Whatsapp Sales' .'%')->get()->pluck('id');

        if ($this->filter === 'week') {
            // 7 hari terakhir
            $startDate = now()->copy()->subDays(6)->startOfDay();
            $sales = Sale::whereDate('created_at', '>=', $startDate)->get();

            $period = CarbonPeriod::create($startDate, now());
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $labels[] = $key;

                $filteredSales = $sales->filter(fn($s) => $s->created_at->format('Y-m-d') === $key);

                $data[] = $filteredSales->sum('total_price');
                $resellerData[] = $filteredSales->where('customer.group_id', 1)->sum('total_price');
                $shopeeData[] = $filteredSales->whereIn('customer.id', $shopee)->sum('total_price');
                $tiktokData[] = $filteredSales->whereIn('customer.id', $tiktok)->sum('total_price');
                $venteData[] = $filteredSales->whereIn('customer.id', $vente)->sum('total_price');

                $dataItem[] = $filteredSales->sum('total_items');
                $resellerDataItem[] = $filteredSales->where('customer.group_id', 1)->sum('total_items');
                $shopeeDataItem[] = $filteredSales->whereIn('customer.id', $shopee)->sum('total_items');
                $tiktokDataItem[] = $filteredSales->whereIn('customer.id', $tiktok)->sum('total_items');
                $venteDataItem[] = $filteredSales->whereIn('customer.id', $vente)->sum('total_items');
            }

        } elseif ($this->filter === 'month') {
            // 12 bulan terakhir per akhir bulan
            $months = collect();
            for ($i = 11; $i >= 0; $i--) {
                $months->push(now()->copy()->subMonths($i)->endOfMonth());
            }

            $sales = Sale::whereDate('created_at', '>=', $months->first()->startOfMonth())->get();

            foreach ($months as $month) {
                $key = $month->format('Y-m');
                $labels[] = $key;

                $filteredSales = $sales->filter(fn($s) => $s->created_at->format('Y-m') === $key);

                $data[] = $filteredSales->sum('total_price');
                $resellerData[] = $filteredSales->where('customer.group_id', 1)->sum('total_price');
                $shopeeData[] = $filteredSales->whereIn('customer.id', $shopee)->sum('total_price');
                $tiktokData[] = $filteredSales->whereIn('customer.id', $tiktok)->sum('total_price');
                $venteData[] = $filteredSales->whereIn('customer.id', $vente)->sum('total_price');

                $dataItem[] = $filteredSales->sum('total_items');
                $resellerDataItem[] = $filteredSales->where('customer.group_id', 1)->sum('total_items');
                $shopeeDataItem[] = $filteredSales->whereIn('customer.id', $shopee)->sum('total_items');
                $tiktokDataItem[] = $filteredSales->whereIn('customer.id', $tiktok)->sum('total_items');
                $venteDataItem[] = $filteredSales->whereIn('customer.id', $vente)->sum('total_items');
            }

        } elseif ($this->filter === 'year') {
            // Semua tahun yang ada di data
            $years = Sale::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->sort();

            $sales = Sale::get();
            foreach ($years as $year) {
                $labels[] = (string) $year;

                $filteredSales = $sales->filter(fn($s) => $s->created_at->format('Y') == $year);
                $data[] = $filteredSales->sum('total_price');
                $resellerData[] = $filteredSales->where('customer.group_id', 1)->sum('total_price');
                $shopeeData[] = $filteredSales->whereIn('customer.id', $shopee)->sum('total_price');
                $tiktokData[] = $filteredSales->whereIn('customer.id', $tiktok)->sum('total_price');
                $venteData[] = $filteredSales->whereIn('customer.id', $vente)->sum('total_price');

                $dataItem[] = $filteredSales->sum('total_items');
                $resellerDataItem[] = $filteredSales->where('customer.group_id', 1)->sum('total_items');
                $shopeeDataItem[] = $filteredSales->whereIn('customer.id', $shopee)->sum('total_items');
                $tiktokDataItem[] = $filteredSales->whereIn('customer.id', $tiktok)->sum('total_items');
                $venteDataItem[] = $filteredSales->whereIn('customer.id', $vente)->sum('total_items');
            }
        }

        // Total terakhir ditampilkan
        $this->total_sale['price'] = end($data);
        $this->total_btc['price'] = end($resellerData);
        $this->total_tiktok['price'] = end($tiktokData);
        $this->total_shopee['price'] = end($shopeeData);
        $this->total_vente['price'] = end($venteData);

        $this->total_sale['items'] = end($dataItem);
        $this->total_btc['items'] = end($resellerDataItem);
        $this->total_shopee['items'] = end($shopeeDataItem);
        $this->total_tiktok['items'] = end($tiktokDataItem);
        $this->total_vente['items'] = end($venteDataItem);

        // Kirim ke JS
        $this->dispatch('update-chart-sales', [
            'salesLabel' => $labels,
            'salesData' => $data,
            'resellerData' => $resellerData,
            'shopeeData' => $shopeeData,
            'tiktokData' => $tiktokData,
            'venteData' => $venteData,
            'salesDataItem' => $dataItem,
            'resellerDataItem' => $resellerDataItem,
            'shopeeDataItem' => $shopeeDataItem,
            'tiktokDataItem' => $tiktokDataItem,
            'venteDataItem' => $venteDataItem,
            'filter' => $this->filter,
        ]);
    }
}
