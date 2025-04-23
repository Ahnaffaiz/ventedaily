<?php

namespace App\Livewire;

use App\Enums\DiscountType;
use App\Models\Discount as ModelsDiscount;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Discount extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;

    public $discount;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $value;

    #[Validate('required')]
    public  $discount_type = DiscountType::PERSEN;

    public $is_active = true;

    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'name' => true,
        'value' => true,
        'discount_type' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Discount')]

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
        return view('livewire.discount', [
            'discounts' =>ModelsDiscount::orderBy($this->sortBy, $this->sortDirection)
                    ->where('name', 'like', '%'.$this->query.'%')
                    ->paginate($this->perPage, ['*'], 'listDiscounts')
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
        ModelsDiscount::firstOrCreate(
            ['name' => $this->name],
                [
                            'value'=>$this->value,
                            'discount_type'=>$this->discount_type,
                            'is_active' => $this->is_active
                        ]);
        $this->closeModal();
        $this->alert('success', 'Discount Succesfully Created');
    }

    public function edit($discount)
    {
        $this->discount = ModelsDiscount::find($discount);
        $this->name = $this->discount->name;
        $this->is_active = $this->discount->is_active;
        $this->value = $this->discount->value;
        $this->discount_type = $this->discount->discount_type;
        $this->isOpen = true;
    }

    public function update() {
        $this->discount->update([
            'name' => $this->name,
            'value' => $this->value,
            'is_active' => $this->is_active,
            'discount_type' => $this->discount_type,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Discount Successfully Updated');
    }

    public function deleteAlert($discount)
    {
        $this->discount = ModelsDiscount::find($discount);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->discount->name .' ?',
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
        $this->discount->delete();
        $this->alert('success', 'Discount Succesfully Deleted');
    }

    public function cancel()
    {
        $this->reset();
    }
}
