<?php

namespace App\Livewire;

use App\Models\Cost as ModelsCost;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Cost extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $cost, $name, $desc;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'desc' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Cost')]

    protected $rules = [
        'name' => 'required|unique:costs',
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
        return view('livewire.cost', [
            'costs' =>ModelsCost::orderBy($this->sortBy, $this->sortDirection)
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
        ModelsCost::firstOrCreate(['name' => $this->name],['desc'=>$this->desc]);
        $this->reset();
        $this->closeModal();
        $this->alert('success', 'Cost Succesfully Created');
    }

    public function edit($cost)
    {
        $this->cost = ModelsCost::find($cost);
        $this->name = $this->cost->name;
        $this->desc = $this->cost->desc;
        $this->isOpen = true;
    }

    public function update() {
        $this->cost->update([
            'name' => $this->name,
            'desc' => $this->desc,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Cost Successfully Updated');
    }

    public function deleteAlert($cost)
    {
        $this->cost = ModelsCost::find($cost);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->cost->name .' ?',
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
        if ($this->cost->expenses->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Cost');
        } else {
            $this->cost->delete();
            $this->alert('success', 'Cost Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }
}
