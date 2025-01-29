<?php

namespace App\Livewire\Product;

use App\Models\Size as ModelsSize;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Size extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $size, $name, $desc;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'desc' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Size')]

    protected $rules = [
        'name' => 'required|unique:sizes',
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

    public function render()
    {
        return view('livewire.product.size', [
            'sizes' =>ModelsSize::orderBy($this->sortBy, $this->sortDirection)
                    ->where('name', 'like', '%'.$this->query.'%')
                    ->paginate($this->perPage)
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
        $this->validate($this->rules);
        ModelsSize::firstOrCreate(['name' => $this->name],['desc'=>$this->desc]);
        $this->closeModal();
        $this->alert('success', 'Size Succesfully Created');
    }

    public function edit($size)
    {
        $this->size = ModelsSize::find($size);
        $this->name = $this->size->name;
        $this->desc = $this->size->desc;
        $this->isOpen = true;
    }

    public function update() {
        $this->size->update([
            'name' => $this->name,
            'desc' => $this->desc,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Size Successfully Updated');
    }

    public function deleteAlert($size)
    {
        $this->size = ModelsSize::find($size);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->size->name .' ?',
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
        if ($this->size->productStocks->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Size');
        } else {
            $this->size->delete();
            $this->alert('success', 'Size Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }
}
