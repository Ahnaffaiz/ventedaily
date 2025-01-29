<?php

namespace App\Livewire\Product;

use App\Models\Color as ModelsColor;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Color extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $color, $name, $desc;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'desc' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Color')]

    protected $rules = [
        'name' => 'required|unique:colors',
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
        return view('livewire.product.color', [
            'colors' =>ModelsColor::orderBy($this->sortBy, $this->sortDirection)
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
        ModelsColor::firstOrCreate(['name' => $this->name],['desc'=>$this->desc]);
        $this->closeModal();
        $this->alert('success', 'Color Succesfully Created');
    }

    public function edit($color)
    {
        $this->color = ModelsColor::find($color);
        $this->name = $this->color->name;
        $this->desc = $this->color->desc;
        $this->isOpen = true;
    }

    public function update() {
        $this->color->update([
            'name' => $this->name,
            'desc' => $this->desc,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Color Successfully Updated');
    }

    public function deleteAlert($color)
    {
        $this->color = ModelsColor::find($color);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->color->name .' ?',
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
        if ($this->color->productStocks->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Color');
        } else {
            $this->color->delete();
            $this->alert('success', 'Color Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }
}
