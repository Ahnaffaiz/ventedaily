<?php

namespace App\Livewire\Keep;

use App\Enums\KeepStatus;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use App\Models\Group;
use App\Models\Keep;
use App\Models\KeepProduct;
use App\Models\Marketplace;
use App\Models\Purchase;
use App\Models\TransferProductStock;
use Exception;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListKeep extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $user;
    public $isOpen = false;
    public $keep;
    public $query = '', $perPage = 10, $sortBy = 'no_keep', $sortDirection = 'desc', $groupIds, $groupId = '', $status = KeepStatus::ACTIVE;

    // Pagination options
    public $perPageOptions = [10, 50, 100, 200];

    public $online_keep_products, $reseller_keep_products, $all_keep_products;
    public $online_keep_price, $reseller_keep_price, $all_keep_price;
    public $total_price;
    public $showColumns = [
        'group' => true,
        'status' => true,
        'keep_time' => true,
        'order_id_marketplace' => true,
        'marketplace_id' => true,
        'total_items' => true,
        'total_price' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    protected $listeners = [
        'delete'
    ];

    #[Title('Keep')]

    public function closeModal()
    {
        $this->isOpen = false;
        $this->keep = null;
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

    public function updatedShowColumns($column)
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }


    public function mount() {
        $this->user = Auth::user();
        $this->groupIds = Group::get();
    }
    public function render()
    {
        //statistic
        $this->all_keep_products = Keep::allTotalItems();
        $this->online_keep_products = Keep::onlineTotalItems();
        $this->reseller_keep_products = Keep::resellerTotalItems();
        $this->all_keep_price = Keep::allTotalPrice();
        $this->online_keep_price = Keep::onlineTotalPrice();
        $this->reseller_keep_price = Keep::resellerTotalPrice();

        return view('livewire.keep.list-keep', [
            'keeps' => KeepProduct::select(
                'keep_products.*',
                'keeps.id as keep_id',
                'keeps.no_keep',
                'keeps.customer_id',
                'keeps.keep_time',
                'keeps.status',
                'keeps.marketplace_id',
                'keeps.order_id_marketplace',
                'marketplaces.name as marketplace_name',
                'customers.name as customer_name',
                'customers.group_id',
                'products.name as product_name',
                'colors.name as color_name',
                'sizes.name as size_name'
            )
                ->join('keeps', 'keep_products.keep_id', '=', 'keeps.id')
                ->join('customers', 'keeps.customer_id', '=', 'customers.id')
                ->leftJoin('marketplaces', 'keeps.marketplace_id', '=', 'marketplaces.id')
                ->join('product_stocks', 'keep_products.product_stock_id', '=', 'product_stocks.id')
                ->join('products', 'product_stocks.product_id', '=', 'products.id')
                ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
                ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
                ->where(function($query) {
                    $query->where('keeps.no_keep', 'like', '%' . $this->query . '%')
                        ->orWhere('keeps.order_id_marketplace', 'like', '%' . $this->query . '%')
                        ->orWhere('customers.name', 'like', '%' . $this->query . '%')
                        ->orWhere('products.name', 'like', '%' . $this->query . '%')
                        ->orWhere('colors.name', 'like', '%' . $this->query . '%')
                        ->orWhere('sizes.name', 'like', '%' . $this->query . '%');
                })
                ->where('customers.group_id', 'like', '%' . $this->groupId . '%')
                ->where('keeps.status', 'like', '%' . $this->status . '%')
                ->orderBy($this->sortBy === 'product_name' ? 'products.name' : 'keeps.' . $this->sortBy, $this->sortDirection)
                ->paginate($this->perPage, ['*'], 'listKeep')
        ]);
    }

    public function show($keep_id) {
        $this->isOpen = true;
        $this->keep = Keep::find($keep_id);
        $this->total_price = array_sum(array_column($this->keep->keepProducts->toArray(), 'total_price'));
    }

    public function deleteAlert($keep)
    {
        $this->keep = Keep::find($keep);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this keep ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'delete',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33',
            'customClass' => [
                'confirmButton' => 'btn bg-primary text-white hover:bg-primary-dark',
                'cancelButton' => 'btn bg-danger text-white hover:bg-danger-dark'
            ]
        ]);
    }

    public function delete()
    {
        try {
            if ($this->keep->status == KeepStatus::ACTIVE) {
                $customer = $this->keep->customer;
                $isReseller = $customer->group->name == 'Reseller' ? true : false;

                foreach ($this->keep->keepProducts as $keepProduct) {
                    // Check if product has been transferred
                    $hasTransferred = TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)->exists();

                    if ($hasTransferred) {
                        // Condition A: Keep is active and product has been transferred
                        // Get ALL transfer product stocks that contain this keep_product_id
                        $transferProductStocks = TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)
                            ->with('transferStock')
                            ->get();

                        foreach ($transferProductStocks as $transferProductStock) {
                            $stockTypeTransfer = $transferProductStock->transferStock->transfer_from;

                            // Get current keep_product_ids array
                            $keepProductIds = $transferProductStock->keep_product_id ?? [];

                            // Remove this specific keep_product_id from the array
                            if (is_array($keepProductIds)) {
                                $keepProductIds = array_filter($keepProductIds, function ($id) use ($keepProduct) {
                                    return $id != $keepProduct->id;
                                });
                                $transferProductStock->update([
                                    'keep_product_id' => array_values($keepProductIds)
                                ]);
                            }
                        }

                        // Restore stock based on customer group
                        if ($isReseller) {
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                                'store_stock' => $keepProduct->productStock->store_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                            ]);

                            // Record stock history
                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::STORE_STOCK,
                                NULL,
                                $keepProduct->home_stock + $keepProduct->store_stock,
                                $this->keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        } else {
                            // For online customers
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                                'home_stock' => $keepProduct->productStock->home_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                            ]);

                            // Record stock history
                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::HOME_STOCK,
                                NULL,
                                $keepProduct->home_stock + $keepProduct->store_stock,
                                $this->keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        }
                    } else {
                        // Condition B: Keep is active but product hasn't been transferred
                        // Restore home_stock
                        if ($keepProduct->home_stock > 0) {
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->home_stock,
                                'home_stock' => $keepProduct->productStock->home_stock + $keepProduct->home_stock,
                            ]);

                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::HOME_STOCK,
                                NULL,
                                $keepProduct->home_stock,
                                $this->keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        }

                        // Restore store_stock
                        if ($keepProduct->store_stock > 0) {
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->store_stock,
                                'store_stock' => $keepProduct->productStock->store_stock + $keepProduct->store_stock,
                            ]);

                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::STORE_STOCK,
                                NULL,
                                $keepProduct->store_stock,
                                $this->keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        }
                    }
                }
                // Condition C: No action needed for keeps with status other than active
            }

            // Delete the keep (and its related keep products through cascade)
            $this->keep->delete();
            $this->alert('success', 'Keep Data Successfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th->getMessage());
        }
    }
}
