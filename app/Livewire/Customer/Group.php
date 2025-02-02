<?php

namespace App\Livewire\Customer;

use App\Models\Group as ModelsGroup;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Group extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $group, $name, $desc;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'desc' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Group')]

    protected $rules = [
        'name' => 'required|unique:groups',
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
        return view('livewire.customer.group', [
            'groups' =>ModelsGroup::orderBy($this->sortBy, $this->sortDirection)
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
        ModelsGroup::firstOrCreate(['name' => $this->name],['desc'=>$this->desc]);
        $this->closeModal();
        $this->alert('success', 'Group Succesfully Created');
    }

    public function edit($group)
    {
        $this->group = ModelsGroup::find($group);
        $this->name = $this->group->name;
        $this->desc = $this->group->desc;
        $this->isOpen = true;
    }

    public function update() {
        $this->group->update([
            'name' => $this->name,
            'desc' => $this->desc,
            'updated_at' => Carbon::now()
        ]);
        $this->closeModal();
        $this->alert('success', 'Group Successfully Updated');
    }

    public function deleteAlert($group)
    {
        $this->group = ModelsGroup::find($group);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->group->name .' ?',
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
        if ($this->group->customers->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Group');
        } else {
            $this->group->delete();
            $this->alert('success', 'Group Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }
}
