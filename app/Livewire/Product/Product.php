<?php

namespace App\Livewire\Product;

use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Imports\ProductImport;
use App\Imports\ProductStockImport;
use App\Models\Category;
use App\Models\Product as ModelsProduct;
use App\Models\ProductPreview;
use App\Models\ProductStock;
use App\Models\ProductStockPreview;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class Product extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    #[Validate('required|unique:products|min:5')]
    public $name='';
    public $desc='';

    #[Validate('required')]
    public $category_id, $is_favorite=false, $imei, $status;

    #[Validate('max:512')]
    public $image, $x_image, $y_image, $width_image, $height_image, $current_image;

    public $productStock;
    public $stockTypes = ['home_stock' => 'Home Stock', 'store_stock' => 'Store Stock', 'pre_order_stock' => 'Pre Order Stock'];
    public $stockFrom='home_stock', $stockTo='store_stock', $stockAmount, $stockTotal;

    public $isOpen = false;
    public $categories, $product, $isProductStock = false, $isStock = false, $isImport = false, $importType = 'product', $isHistory = false;

    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';

    public $start_date, $end_date;

    //import
    #[Validate('required')]
    public $product_file;

    public $productPreviews;

    #[Validate('required')]
    public $stock_file;

    public $stockPreviews;
    public $openRows = [];

    public $showColumns = [
        'category_id' => true,
        'all_stock' => true,
        'home_stock' => true,
        'store_stock' => true,
        'pre_order_stock' => true,
    ];

    #[Title('Product')]

    protected $listeners = [
        'delete',
        'saveProduct',
        'saveProductStock',
    ];

    public function updatedQuery()
    {
        $this->resetPage();
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

    public function toggleRow($productId)
    {
        if (in_array($productId, $this->openRows)) {
            $this->openRows = array_diff($this->openRows, [$productId]);
        } else {
            $this->openRows[] = $productId;
        }
    }


    public function mount()
    {
        $this->categories = Category::all()->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.product.product', [
            'products' => ModelsProduct::withSum('productStocks', 'all_stock')
                ->withSum('productStocks', 'home_stock')
                ->withSum('productStocks', 'store_stock')
                ->withSum('productStocks', 'pre_order_stock')
                ->where('name', 'like', '%'.$this->query.'%')
                ->orderBy(
                    match ($this->sortBy) {
                        'all_stock' => 'product_stocks_sum_all_stock',
                        'home_stock' => 'product_stocks_sum_home_stock',
                        'store_stock' => 'product_stocks_sum_store_stock',
                        'pre_order_stock' => 'product_stocks_sum_pre_order_stock',
                        default => $this->sortBy,
                    },
                    $this->sortDirection
                )
                ->paginate($this->perPage, ['*'], 'listProducts')
        ]);
    }


    public function openModal()
    {
        $this->reset();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function save()
    {

        try {
            $image = null;
            if($this->image){
                $width_image = intval(round($this->width_image));
                $height_image = intval(round($this->height_image));
                $x_image = intval(round($this->x_image));
                $y_image = intval(round($this->y_image));

                $cropped_image = Image::make($this->image->getRealPath());
                $cropped_image->crop($width_image, $height_image, $x_image, $y_image);
                $cropped_image->save();

                // store logo left to storage
                $ekstensi = $this->image->getClientOriginalExtension();
                $image =  'image' . "." . $ekstensi;
                $image = 'products/'.$image;
                Storage::disk('public')->put($image, $cropped_image, 'public');

                $this->current_image = $image;
            }
            ModelsProduct::updateOrCreate(['name' => $this->name],[
                'category_id' => $this->category_id,
                'is_favorite' => $this->is_favorite,
                'imei' => $this->imei,
                'code' => Str::random(10),
                'status' => $this->status,
                'desc' => $this->desc,
                'image' => $image,
            ]);
            $this->alert('success', 'Product Successfully Created');
            $this->closeModal();
            $this->reset();
            $this->mount();
        } catch (Exception $th) {
            $this->alert('error', 'Can\'t Create Product', [
                'text' => $th->getMessage()
            ]);
        }
    }

    public function edit($product)
    {
        $this->product = ModelsProduct::find($product);
        $this->name = $this->product->name;
        $this->desc = $this->product->desc;
        $this->category_id = $this->product->category_id;
        $this->category_id = $this->product->category_id;
        $this->is_favorite = (bool) $this->product->is_favorite;
        $this->imei = $this->product->imei;
        $this->status = $this->product->status->value;
        $this->current_image = $this->product->image;
        $this->isOpen = true;
    }

    public function update() {
        if(!$this->isProductStock) {
            $path = $this->current_image;
            if($this->image){
                if($this->current_image != null) {
                    if (Storage::disk('public')->exists($this->current_image)) {
                        Storage::disk('public')->delete($this->current_image);
                    }
                }
                $path = $this->image->store('products', 'public');
                $this->current_image = $path;
            }

            $this->product->update([
                'name' => $this->name,
                'desc' => $this->desc,
                'image' => $path,
                'category_id' => $this->category_id,
                'is_favorite' => $this->is_favorite,
                'imei' => $this->imei,
                'status' => $this->status,
                'updated_at' => Carbon::now()
            ]);
            $this->closeModal();
            $this->alert('success', 'Product Successfully Updated');
        } else {
            $this->closeModal();
            $this->alert('success', 'Product Successfully Updated');
        }

    }

    public function deleteImage()
    {
        $this->image = null;
        $this->current_image = null;
    }

    public function deleteAlert($product)
    {
        $this->product = ModelsProduct::find($product);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->product->name .' ?',
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
        if ($this->product->productStocks->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Product');
        } else {
            $this->product->delete();
            $this->alert('success', 'Product Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }

    public function addProductStock($product)
    {
        $this->product = ModelsProduct::find($product);
        $this->isProductStock = true;
        $this->isOpen = true;
    }

    public function transferStock($productStock)
    {
        $this->productStock = ProductStock::find($productStock);
        $this->stockAmount = $this->productStock->home_stock;
        $this->stockTotal = $this->productStock->home_stock;
        $this->isStock = true;
        $this->isOpen = true;
    }

    public function updatedStockFrom()
    {
        if($this->stockFrom == 'home_stock') {
            $this->stockTotal = $this->productStock->home_stock;
            $this->stockAmount = $this->productStock->home_stock;
            $this->stockTo = 'store_stock';
        } elseif( $this->stockFrom == 'store_stock') {
            $this->stockAmount = $this->productStock->store_stock;
            $this->stockTotal = $this->productStock->store_stock;
            $this->stockTo = 'home_stock';
        } elseif ($this->stockFrom == 'pre_order_stock') {
            $this->stockAmount = $this->productStock->pre_order_stock;
            $this->stockTotal = $this->productStock->pre_order_stock;
            $this->stockTo = 'home_stock';
        }
    }

    public function updatedStockTo()
    {
        if($this->stockTo == $this->stockFrom && $this->stockTo == 'home_stock') {
            $this->stockFrom = 'store_stock';
        } elseif( $this->stockTo == $this->stockFrom && $this->stockTo == 'store_stock') {
            $this->stockFrom = 'home_stock';
        } elseif ($this->stockTo == $this->stockFrom && $this->stockTo == 'pre_order_stock') {
            $this->stockFrom = 'home_stock';
        }
    }

    public function generateImei()
    {
        do {
            $this->imei = Str::random(20);
        } while (ModelsProduct::where('imei', $this->imei)->exists());
    }

    public function saveStock()
    {
        if($this->stockAmount < 1) {
            $this->alert('warning', 'Stock amount invalid');
        } elseif($this->stockAmount > $this->stockTotal) {
            $this->alert('warning', 'Insufficient stock');
        } else {
            $stockTo = $this->stockTo;
            $stockFrom = $this->stockFrom;

            $this->productStock->update([
                $this->stockTo => $this->productStock->$stockTo + $this->stockAmount,
                $this->stockFrom => $this->productStock->$stockFrom - $this->stockAmount,
            ]);

            setStockHistory(
                $this->productStock->id,
                StockActivity::TRANSFER,
                StockStatus::CHANGE,
                $this->stockFrom,
                $this->stockTo,
                $this->stockAmount,
                NULL,
                $this->productStock->all_stock,
                $this->productStock->home_stock,
                $this->productStock->store_stock,
                $this->productStock->pre_order_stock,
            );

            $this->alert('success','Stock successfully transfered');
            $this->stockAmount = null;
            $this->stockTotal = null;
            $this->stockFrom = 'home_stock';
            $this->stockTo = 'store_stock';
            $this->isOpen = false;
        }

    }

    public function openImportModal($importType)
    {
        $this->importType = $importType;
        $this->isImport = true;
        if($this->importType == 'product') {
            $this->productPreviews = ProductPreview::get();
        } else {
            $this->stockPreviews = ProductStockPreview::get();
        }
        $this->isOpen = true;
    }

    public function previewProduct()
    {
        try {
            $this->validateOnly('product_file');
            ProductPreview::truncate();
            Excel::import(new ProductImport, $this->product_file);
            $this->productPreviews = ProductPreview::get();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function saveProduct()
    {
        try {
            $error = ProductPreview::where('error', '==', [])->first();
            if($error) {
                $this->alert('error', 'Please solve the error first');
            } else {
                foreach ($this->productPreviews as $product) {
                    ModelsProduct::updateOrCreate(
                        [
                        'name' => $product->name,
                        'category_id' => $product->category_id],
                    [
                        'imei' => $product->imei,
                        'code' => $product->code,
                        'status' => $product->status,
                        'desc' => $product->desc
                    ]);
                }
                ProductPreview::truncate();
                $this->alert('success','Product Successfully Imported');
                return $this->reset();
            }
        } catch (\Throwable $th) {
            $this->alert('error', 'Silahkan Perbaiki Errornya terlebih dahulu');
        }
    }

    public function resetProductPreview()
    {
        ProductPreview::truncate();
        $this->productPreviews = null;
    }

    public function saveProductAlert()
    {
        $this->alert('question', 'Yakin melakukan Import Product ?', [
            'toast' => false,
            'text' => 'Import Product tidak bisa dibatalkan',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'saveProduct',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function previewProductStock()
    {
        try {
            $this->validateOnly('stock_file');
            ProductStockPreview::truncate();
            Excel::import(new ProductStockImport, $this->stock_file);
            $this->stockPreviews = ProductStockPreview::get();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function saveProductStock()
    {
        try {
            $error = ProductStockPreview::where('error', '==', [])->first();
            if($error) {
                $this->alert('error', 'Please solve the error first');
            } else {
                foreach ($this->stockPreviews as $productStock) {
                    $stock = ProductStock::updateOrCreate(
                        [
                        'product_id' => $productStock->product_id,
                        'color_id' => $productStock->color_id,
                        'size_id' => $productStock->size_id],
                    [
                        'status' => $productStock->status,
                        'purchase_price' => $productStock->purchase_price,
                        'selling_price' => $productStock->selling_price,
                        'home_stock' => $productStock->home_stock,
                        'store_stock' => $productStock->store_stock,
                        'pre_order_stock' => $productStock->pre_order_stock,
                        'all_stock' => $productStock->all_stock,
                        'qc_stock' => 0,
                        'vermak_stock' => 0,
                    ]);

                    setStockHistory(
                        $stock->id,
                        StockActivity::IMPORT,
                        StockStatus::ADD,
                        NULL,
                        NULL,
                        $stock->all_stock,
                        NULL,
                        $stock->all_stock,
                        $stock->home_stock,
                        $stock->store_stock,
                        $stock->pre_order_stock,
                    );
                }
                ProductStockPreview::truncate();
                $this->alert('success','Product Stock Successfully Imported');
                return $this->reset();
            }
        } catch (\Throwable $th) {
            info($th);
            $this->alert('error', 'Silahkan Perbaiki Errornya terlebih dahulu');
        }
    }

    public function resetProductStockPreview()
    {
        ProductStockPreview::truncate();
        $this->stockPreviews = null;
    }

    public function saveProductStockAlert()
    {
        $this->alert('question', 'Yakin melakukan Import Stock ?', [
            'toast' => false,
            'text' => 'Import Stock tidak bisa dibatalkan',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'saveProductStock',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function openModelHistory($productStockId)
    {
        $this->productStock = ProductStock::find($productStockId);
        $this->isHistory = true;
        $this->isOpen = true;
    }

    public function showHistory()
    {
        $url = route('product-stock-history', [
            'productStockId' => $this->productStock->id,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
        ]);

        $this->dispatch('openStockHistoryTab', $url);
    }
}
