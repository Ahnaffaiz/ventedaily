<?php

namespace App\Livewire\Customer;

use App\Models\Customer as ModelsCustomer;
use App\Models\Group;
use App\Models\Size;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Customer extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $customer;

    #[Validate('required|unique:customers|min:5')]
    public $name;

    #[Validate('required|regex:/^8\d+$/')]
    public $phone;

    #[Validate('required|email|max:255')]
    public $email;

    #[Validate('required')]
    public $address;

    #[Validate('required')]
    public $group_id;

    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'phone' => true,
        'email' => true,
        'address' => true,
        'group_id' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Customer')]

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
        return view('livewire.customer.customer', [
            'customers' =>ModelsCustomer::orderBy($this->sortBy, $this->sortDirection)
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
        $this->validate();
        ModelsCustomer::firstOrCreate(['name' => $this->name],
        [
                    'phone'=>$this->phone,
                    'email'=>$this->email,
                    'address'=>$this->address,
                    'group_id'=>$this->group_id,
            ]);
        $this->closeModal();
        $this->alert('success', 'Customer Succesfully Created');
    }

    public function edit($customer)
    {
        $this->customer = ModelsCustomer::find($customer);
        $this->name = $this->customer->name;
        $this->phone = $this->customer->phone;
        $this->email = $this->customer->email;
        $this->address = $this->customer->address;
        $this->group_id = $this->customer->group_id;
        $this->isOpen = true;
    }

    public function update() {
        $this->customer->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'group_id' => $this->group_id,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Customer Successfully Updated');
    }

    public function deleteAlert($customer)
    {
        $this->customer = ModelsCustomer::find($customer);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->customer->name .' ?',
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
        if ($this->customer->keeps->count() > 0 || $this->customer->sales->count() > 0 ) {
            $this->alert('warning', 'Can\'t Delete Customer');
        } else {
            $this->customer->delete();
            $this->alert('success', 'Customer Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }
}
