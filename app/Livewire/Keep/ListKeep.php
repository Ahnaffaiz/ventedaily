<?php

namespace App\Livewire\Keep;

use App\Enums\KeepStatus;
use App\Models\Group;
use App\Models\Keep;
use App\Models\Purchase;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListKeep extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $isOpen = false;
    public $keep;
    public $query = '', $perPage = 10, $sortBy = 'no_keep', $sortDirection = 'asc', $groupIds, $groupId = '', $status = KeepStatus::ACTIVE;
    public $total_price;
    public $showColumns = [
        'status' => true,
        'keep_time' => true,
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


    public function mount() {
        $this->groupIds = Group::get();
    }
    public function render()
    {
        return view('livewire.keep.list-keep', [
            'keeps' => Keep::select('keeps.*')
                ->join('customers', 'keeps.customer_id', '=', 'customers.id')
                ->where('customers.name', 'like', '%' . $this->query . '%')
                ->where('customers.group_id', 'like', '%' . $this->groupId . '%')
                ->where('status', 'like', '%' . $this->status . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
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
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function delete()
    {
        try {
            foreach ($this->keep->keepProducts as $keepProduct) {
                $keepProduct->productStock->update([
                    'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->total_items,
                    'home_stock' => $keepProduct->productStock->home_stock + $keepProduct->home_stock,
                    'store_stock' => $keepProduct->productStock->store_stock + $keepProduct->store_stock,
                ]);
            }
            $this->keep->delete();
            $this->alert('success', 'Keep Data Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th);
        }
    }
}
