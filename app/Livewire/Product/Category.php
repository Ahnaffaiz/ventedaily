<?php

namespace App\Livewire\Product;

use App\Imports\CategoryImport;
use App\Models\Category as ModelsCategory;
use App\Models\CategoryPreview;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Category extends Component
{
    use LivewireAlert, WithFileUploads;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false, $isImport = false;
    public $category, $name, $desc, $category_file, $categoryPreviews;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $perPageOptions = [10, 50, 100, 200];
    public $showColumns = [
        'desc' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Category')]

    protected $rules = [
        'name' => 'required|unique:categories',
    ];

    protected $listeners = [
        'delete'
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.product.category', [
            'categories' =>ModelsCategory::orderBy($this->sortBy, $this->sortDirection)
                    ->where('name', 'like', '%'.$this->query.'%')
                    ->paginate($this->perPage, ['*'], 'listCategories'),
        ]);
    }

    public function openModal()
    {
        $this->reset();
        $this->isOpen = true;
    }

    public function openModalImport()
    {
        $this->reset();
        $this->categoryPreviews = CategoryPreview::get();
        if($this->categoryPreviews->count() <= 0) {
            $this->categoryPreviews = null;
        }
        $this->isImport = true;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function save()
    {
        $this->validate($this->rules);
        ModelsCategory::firstOrCreate(['name' => $this->name],['desc'=>$this->desc]);
        $this->reset();
        $this->closeModal();
        $this->alert('success', 'Category Succesfully Created');
    }

    public function edit($category)
    {
        $this->category = ModelsCategory::find($category);
        $this->name = $this->category->name;
        $this->desc = $this->category->desc;
        $this->isOpen = true;
    }

    public function update() {
        $this->category->update([
            'name' => $this->name,
            'desc' => $this->desc,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Category Successfully Updated');
    }

    public function deleteAlert($category)
    {
        $this->category = ModelsCategory::find($category);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->category->name .' ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
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
        if ($this->category->products->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Category');
        } else {
            $this->category->delete();
            $this->alert('success', 'Category Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }

    public function previewImport()
    {
        try {
            CategoryPreview::truncate();
            Excel::import(new CategoryImport, $this->category_file);
            $this->categoryPreviews = CategoryPreview::get();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function saveCategory()
    {
        $error = CategoryPreview::where('error', '!=', null)->first();
        if($error) {
            $this->alert('error', 'Please solve the error first');
        } else {
            foreach ($this->categoryPreviews as $category) {
                ModelsCategory::firstOrCreate([
                    'name' => $category->name,
                    'desc' => $category->desc,
                ]);
            }
            CategoryPreview::truncate();
            $this->alert('success','Category Successfully Imported');
            $this->reset();
        }
    }

    public function resetCategoryPreview()
    {
        CategoryPreview::truncate();
        $this->categoryPreviews = null;
    }
}
