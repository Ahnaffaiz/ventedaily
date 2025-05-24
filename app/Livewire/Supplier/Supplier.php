<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier as ModelsSupplier;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Supplier extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;

    public $supplier, $name, $phone, $address;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $perPageOptions = [10, 50, 100, 200];
    public $showColumns = [
        'phone' => true,
        'address' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Supplier')]

    protected $rules = [
        'name' => 'required|unique:suppliers',
        'phone' => 'regex:/^8\d+$/',
        'address'=> 'required',
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
        return view('livewire.supplier.supplier', [
            'suppliers' =>ModelsSupplier::orderBy($this->sortBy, $this->sortDirection)
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
        ModelsSupplier::firstOrCreate(
            ['name' => $this->name],
                [
                            'phone'=>'62'.$this->phone,
                            'address'=>$this->address
                        ]);
        $this->closeModal();
        $this->alert('success', 'Supplier Succesfully Created');
    }

    public function edit($supplier)
    {
        $this->supplier = ModelsSupplier::find($supplier);
        $this->name = $this->supplier->name;
        $this->phone = $this->supplier->phone;
        $this->address = $this->supplier->address;
        $this->isOpen = true;
    }

    public function update() {
        $this->supplier->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Supplier Successfully Updated');
    }

    public function deleteAlert($supplier)
    {
        $this->supplier = ModelsSupplier::find($supplier);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->supplier->name .' ?',
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
        if ($this->supplier->purchases->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Supplier');
        } else {
            $this->supplier->delete();
            $this->alert('success', 'Supplier Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }
}
