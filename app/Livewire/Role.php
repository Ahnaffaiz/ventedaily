<?php

namespace App\Livewire;

use App\Models\User as ModelsUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $role;

    #[Rule('required')]
    public $name;

    public $permission_ids = [], $permissions;

    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $perPageOptions = [10, 50, 100, 200];
    public $showColumns = [
        'name' => true,
        'permission' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('Role')]

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'name' : 'asc';
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
        return view('livewire.role', [
            'roles' =>ModelsRole::orderBy($this->sortBy, $this->sortDirection)
                    ->where('name', 'like', '%'.$this->query.'%')
                    ->paginate($this->perPage)
        ]);
    }

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function edit($role)
    {
        $this->role = ModelsRole::find($role);
        $this->name = $this->role->name;
        $this->permissions = Permission::orderBy('created_at', 'asc')->pluck('name','id')->toArray();
        $this->permission_ids = $this->role->permissions->pluck('name');
        $this->isOpen = true;
    }

    public function update() {
        $this->role->syncPermissions($this->permission_ids);
        $this->reset();
        $this->closeModal();
        $this->alert('success', 'Permission Successfully Updated');
    }

    public function cancel()
    {
        $this->reset();
    }
}
