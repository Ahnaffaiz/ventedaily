<?php

namespace App\Livewire\Product\TransferStock;

use App\Enums\KeepStatus;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use App\Exports\TransferStockExport;
use App\Exports\TransferStockInExport;
use App\Models\Keep;
use App\Models\KeepProduct;
use App\Models\ProductStock;
use App\Models\TransferProductStock;
use App\Models\TransferStock;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListTransferStock extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $isOpen = false, $isExport = false;
    public $transferStock;
    public $query = '', $perPage = 10, $sortBy = 'created_at', $sortDirection = 'desc';
    public $total_price;

    #[Rule('required')]
    public $start_date, $end_date;
    public $stockFrom, $stockTo;

    public $isStockFrom = "all_stock";

    public $transferToStores, $transferToHomes;
    public $transferToStoreJunis, $transferToHomeJunis;

    public $transfer_from, $transfer_to;
    public $cart;

    protected $listeners = [
        'delete',
        'transferProduct'
    ];

    #[Title('Transfer Stock')]

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $column;
    }

    public function mount()
    {
        $this->getTransferStockToStore();
        $this->getTransferStockToHome();
        $this->getTransferStockToStoreJuni();
        $this->getTransferStockToHomeJuni();
    }

    public function render()
    {
        return view('livewire.product.transfer-stock.list-transfer-stock', [
            'transferStocks' => TransferStock::select('transfer_stocks.*')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function show($transfer_stock_id) {
        $this->isOpen = true;
        $this->transferStock = TransferStock::find($transfer_stock_id);
    }

    public function getTransferStockToStore()
    {
        $transferToStores = KeepProduct::leftJoin('transfer_product_stocks', function($join) {
            $join->on('transfer_product_stocks.keep_product_id', 'like', DB::raw("CONCAT('%', keep_products.id, '%')"));
        })
        ->join('product_stocks', 'keep_products.product_stock_id', 'product_stocks.id')
        ->join('products', 'product_stocks.product_id', 'products.id')
        ->join('colors', 'product_stocks.color_id', 'colors.id')
        ->join('sizes', 'product_stocks.size_id', 'sizes.id')
        ->whereNull('transfer_product_stocks.keep_product_id')
        ->whereHas('keep', function($query)  {
            return $query->whereHas('customer', function($query) {
                return $query->where('group_id', 1);
            });
        })
        ->where('keep_products.home_stock', '!=', 0)
        ->select([
            'keep_products.id as id',
            'keep_products.product_stock_id as product_stock_id',
            'products.name as name',
            'colors.name as color',
            'sizes.name as size',
            'keep_products.home_stock as stock'
        ])
        ->get();

        foreach ($transferToStores as $keepProduct) {
            $productStockId = $keepProduct->product_stock_id;
            if (isset($this->transferToStores[$productStockId])) {
                $this->transferToStores[$productStockId]['stock'] += $keepProduct['stock'];
                $this->transferToStores[$productStockId]['keep_product_id'][] = $keepProduct['id'];
            } else {
                $this->transferToStores[$productStockId] = [
                    'id' => $keepProduct['product_stock_id'],
                    'name' => $keepProduct['name'],
                    'color' => $keepProduct['color'],
                    'size' => $keepProduct['size'],
                    'keep_product_id' => [$keepProduct['id']],
                    'stock' => $keepProduct['stock'],
                ];
            }
        }
    }

    public function getTransferStockToHome()
    {
        $transferToHomes = KeepProduct::leftJoin('transfer_product_stocks', function($join) {
            $join->on('transfer_product_stocks.keep_product_id', 'like', DB::raw("CONCAT('%', keep_products.id, '%')"));
        })
        ->join('product_stocks', 'keep_products.product_stock_id', 'product_stocks.id')
        ->join('products', 'product_stocks.product_id', 'products.id')
        ->join('colors', 'product_stocks.color_id', 'colors.id')
        ->join('sizes', 'product_stocks.size_id', 'sizes.id')
        ->whereNull('transfer_product_stocks.keep_product_id')
        ->whereHas('keep', function($query) {
            return $query->whereHas('customer', function($query) {
                return $query->where('group_id', 2);
            });
        })
        ->where('keep_products.store_stock', '!=', 0)
        ->select([
            'keep_products.id as id',
            'keep_products.product_stock_id as product_stock_id',
            'products.name as name',
            'colors.name as color',
            'sizes.name as size',
            'keep_products.store_stock as stock'
        ])
        ->get();


        foreach ($transferToHomes as $keepProduct) {
            $productStockId = $keepProduct->product_stock_id;
            if (isset($this->transferToHomes[$productStockId])) {
                $this->transferToHomes[$productStockId]['stock'] += $keepProduct['stock'];
                $this->transferToHomes[$productStockId]['keep_product_id'][] = $keepProduct['id'];
            } else {
                $this->transferToHomes[$productStockId] = [
                    'id' => $keepProduct['product_stock_id'],
                    'name' => $keepProduct['name'],
                    'color' => $keepProduct['color'],
                    'size' => $keepProduct['size'],
                    'keep_product_id' => [$keepProduct['id']],
                    'stock' => $keepProduct['stock'],
                ];
            }
        }
    }

    public function getTransferStockToStoreJuni()
    {
        $date = Carbon::parse('14 April 2025');
        $yesterday = Keep::where('no_keep', 'A0281')->first();
        $dataYesterday = Keep::where('id', '<=', $yesterday->id)
                ->whereDate('created_at', $date)
                ->whereHas('customer', function($query){
                    $query->where('group_id', 1);
                })->get()->pluck('id');
        $transferToStoreJunis = KeepProduct::leftJoin('transfer_product_stocks', function($join) {
            $join->on('transfer_product_stocks.keep_product_id', 'like', DB::raw("CONCAT('%', keep_products.id, '%')"));
        })
        ->join('product_stocks', 'keep_products.product_stock_id', 'product_stocks.id')
        ->join('products', 'product_stocks.product_id', 'products.id')
        ->join('colors', 'product_stocks.color_id', 'colors.id')
        ->join('sizes', 'product_stocks.size_id', 'sizes.id')
        ->whereNull('transfer_product_stocks.keep_product_id') // Pastikan bahwa KeepProduct belum ada di transfer_product_stocks
        ->whereDate('keep_products.created_at', $date)
        ->whereHas('keep', function($query) use ($dataYesterday) {
            return $query->whereHas('customer', function($query) {
                return $query->where('group_id', 1);
            })->whereIn('id', $dataYesterday);
        })
        ->where('keep_products.home_stock', '!=', 0)
        ->select([
            'keep_products.id as id',
            'keep_products.product_stock_id as product_stock_id',
            'products.name as name',
            'colors.name as color',
            'sizes.name as size',
            'keep_products.home_stock as stock'
        ])
        ->get();


        foreach ($transferToStoreJunis as $keepProduct) {
            $productStockId = $keepProduct->product_stock_id;
            if (isset($this->transferToStoreJunis[$productStockId])) {
                $this->transferToStoreJunis[$productStockId]['stock'] += $keepProduct['stock'];
                $this->transferToStoreJunis[$productStockId]['keep_product_id'][] = $keepProduct['id'];
            } else {
                $this->transferToStoreJunis[$productStockId] = [
                    'id' => $keepProduct['product_stock_id'],
                    'name' => $keepProduct['name'],
                    'color' => $keepProduct['color'],
                    'size' => $keepProduct['size'],
                    'keep_product_id' => [$keepProduct['id']],
                    'stock' => $keepProduct['stock'],
                ];
            }
        }
    }

    public function getTransferStockToHomeJuni()
    {
        $date = Carbon::parse('14 April 2025');
        $yesterday = Keep::where('no_keep', 'A0281')->first();
        $dataYesterday = Keep::where('id', '<=', $yesterday->id)
            ->whereDate('created_at', $date)
            ->whereHas('customer', function($query){
                $query->where('group_id', 2);
            })->get()->pluck('id');
        $transferToHomeJunis = KeepProduct::leftJoin('transfer_product_stocks', function($join) {
            $join->on('transfer_product_stocks.keep_product_id', 'like', DB::raw("CONCAT('%', keep_products.id, '%')"));
        })
        ->join('product_stocks', 'keep_products.product_stock_id', 'product_stocks.id')
        ->join('products', 'product_stocks.product_id', 'products.id')
        ->join('colors', 'product_stocks.color_id', 'colors.id')
        ->join('sizes', 'product_stocks.size_id', 'sizes.id')
        ->whereNull('transfer_product_stocks.keep_product_id') // Pastikan bahwa KeepProduct belum ada di transfer_product_stocks
        ->whereDate('keep_products.created_at', $date)
        ->whereHas('keep', function($query) use ($dataYesterday) {
            return $query->whereHas('customer', function($query) {
                return $query->where('group_id', 2);
            })->whereIn('id', $dataYesterday);
        })
        ->where('keep_products.store_stock', '!=', 0)
        ->select([
            'keep_products.id as id',
            'keep_products.product_stock_id as product_stock_id',
            'products.name as name',
            'colors.name as color',
            'sizes.name as size',
            'keep_products.store_stock as stock'
        ])
        ->get();


        foreach ($transferToHomeJunis as $keepProduct) {
            $productStockId = $keepProduct->product_stock_id;
            if (isset($this->transferToHomeJunis[$productStockId])) {
                $this->transferToHomeJunis[$productStockId]['stock'] += $keepProduct['stock'];
                $this->transferToHomeJunis[$productStockId]['keep_product_id'][] = $keepProduct['id'];
            } else {
                $this->transferToHomeJunis[$productStockId] = [
                    'id' => $keepProduct['product_stock_id'],
                    'name' => $keepProduct['name'],
                    'color' => $keepProduct['color'],
                    'size' => $keepProduct['size'],
                    'keep_product_id' => [$keepProduct['id']],
                    'stock' => $keepProduct['stock'],
                ];
            }
        }
    }

    public function transferProductAlert($stockType)
    {
        $this->transfer_to = $stockType == 'store' ? 'store_stock' : 'home_stock';
        $this->transfer_from = $stockType == 'store' ? 'home_stock' : 'store_stock';
        $this->alert('question', 'Transfer Product to ' . ucwords($stockType), [
            'toast' => false,
            'text' => 'Create Transfer Stock ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'transferProduct',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function transferProduct()
    {
        try {
            if($this->transfer_to == 'store_stock') {
                $this->cart = $this->transferToStores;
            } elseif($this->transfer_to == 'home_stock') {
                $this->cart = $this->transferToHomes;
            }
            $total_items = array_sum(array_column($this->cart, 'stock'));
            if($total_items > 0) {
                $transferStock = TransferStock::create([
                    'user_id' => Auth::user()->id,
                    'transfer_from' => strtolower($this->transfer_from),
                    'transfer_to' => strtolower($this->transfer_to),
                    'total_items' => $total_items,
                ]);

                $this->createTransferProductStock($transferStock->id);
                $this->reset();
                $this->alert('success', 'Transfer Succesfully Created');
                return redirect()->route('create-transfer-stock', $transferStock->id);
            } else {
                $this->alert('warning', 'No Product To Transfer');
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function transferProduct14Juni($stockType)
    {
        try {
            if($stockType == 'store') {
                $this->transfer_to = 'store_stock';
                $this->transfer_from = 'home_stock';
                $this->cart = $this->transferToStoreJunis;
            } elseif($stockType == 'home') {
                $this->transfer_to = 'home_stock';
                $this->transfer_from = 'store_stock';
                $this->cart = $this->transferToHomeJunis;
            }
            $total_items = array_sum(array_column($this->cart, 'stock'));
            if($total_items > 0) {
                $transferStock = TransferStock::create([
                    'user_id' => Auth::user()->id,
                    'transfer_from' => strtolower($this->transfer_from),
                    'transfer_to' => strtolower($this->transfer_to),
                    'total_items' => $total_items,
                ]);

                $this->createTransferProductStock($transferStock->id);
                $this->reset();
                $this->alert('success', 'Transfer Succesfully Created');
                return redirect()->route('create-transfer-stock', $transferStock->id);
            } else {
                $this->alert('warning', 'No Product To Transfer');
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function createTransferProductStock($transferStockId)
    {
        foreach ($this->cart as $cart) {
            TransferProductStock::create([
                'transfer_stock_id' => $transferStockId,
                'product_stock_id' => $cart['id'],
                'stock' => $cart['stock'],
                'keep_product_id' => $cart['keep_product_id'],
            ]);
        }
    }

    public function exportTransferProduct($transferStockId)
    {
        $transferStock = TransferStock::where('id', $transferStockId)->first();
        $name = "Tranfser Produk Dari " . ucwords(str_replace('_', ' ', $transferStock->transfer_from)) . " Ke " . ucwords(str_replace('_', ' ', $transferStock->transfer_to)) . " Tanggal " . Carbon::parse($transferStock->created_at)->format('d F Y')  .".xlsx";
        return Excel::download(new TransferStockExport($transferStockId), $name);
    }

    public function deleteAlert($transferStock)
    {
        $this->transferStock = TransferStock::find($transferStock);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this Transfer Data ?',
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
        try {
            foreach ($this->transferStock->transferProducts as $transferProduct) {
                $productStock = ProductStock::where('id', $transferProduct->product_stock_id)->first();
                $productStock->update([
                    $this->transferStock->transfer_from => $productStock[$this->transferStock->transfer_from] + $transferProduct->stock,
                    $this->transferStock->transfer_to => $productStock[$this->transferStock->transfer_to] - $transferProduct->stock,
                ]);
                setStockHistory(
                    $productStock->id,
                    StockActivity::TRANSFER,
                    StockStatus::REMOVE,
                    $this->transferStock->transfer_to,
                    $this->transferStock->transfer_from,
                    $transferProduct->stock,
                    NULL,
                    $productStock->all_stock,
                    $productStock->home_stock,
                    $productStock->store_stock,
                    $productStock->pre_order_stock,
                );

                $transferProduct->delete();
            }
            $this->transferStock->delete();
            $this->alert('success', 'Transfer Data Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function openModalExport()
    {
        $this->isExport = true;
        $this->isOpen = true;
    }

    public function updatedIsStockFrom()
    {
        if($this->isStockFrom == 'specific_stock') {
            $this->stockFrom = StockType::HOME_STOCK;
            $this->stockTo = StockType::STORE_STOCK;
        }
    }

    public function exportExcel()
    {
        $this->validate();
        $name = "Transfer Produk ". ucwords(str_replace('_', ' ', $this->stockFrom)) . "Ke " . ucwords(str_replace('_', ' ', $this->stockTo)) .  " Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
        if($this->isStockFrom == 'specific_stock') {
            return Excel::download(new TransferStockInExport($this->start_date, $this->end_date, $this->stockFrom, $this->stockTo), $name);
        } else {
            return Excel::download(new TransferStockInExport($this->start_date, $this->end_date), $name);
        }
    }
}
