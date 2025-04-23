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
use Spatie\Permission\Models\Role;

class User extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $user;

    #[Rule('required')]
    public $name;

    #[Rule('required | string | min:8 | regex:/[A-Z]/ | regex:/[0-9]/ | regex:/[\W]/')]
    public $password;

    public $showPassword = false;

    public $role_ids = [], $roles;

    #[Rule('required|unique:users')]
    public $email;

    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'name' => true,
        'email' => true,
        'role' => true,
        'created_at' => true,
        'updated_at' => true,
    ];

    #[Title('User')]

    protected $listeners = [
        'delete'
    ];

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function passwordToggle()
    {
        $this->showPassword = !$this->showPassword;
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

    public function render()
    {
        return view('livewire.user', [
            'users' =>ModelsUser::orderBy($this->sortBy, $this->sortDirection)
                    ->where('name', 'like', '%'.$this->query.'%')
                    ->paginate($this->perPage)
        ]);
    }

    public function openModal()
    {
        $this->reset();
        $this->roles = Role::all()->pluck('name','id')->toArray();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function save()
    {
        try {
            $this->validate();
        $user = ModelsUser::create([
            'email' => $this->email,
            'name' => $this->name,
            'password'=> Hash::make($this->password),
        ]);

        $user->assignRole($this->role_ids);

        $this->reset();
        $this->closeModal();
        $this->alert('success', 'User Succesfully Created');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function edit($user)
    {
        $this->user = ModelsUser::find($user);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->roles = Role::all()->pluck('name','id')->toArray();
        $this->role_ids = $this->user->roles->pluck('name');
        $this->isOpen = true;
    }

    public function update() {
        $this->validate([
            'name'=> ['required'],
            'email' => ['required']
        ]);
        if($this->password) {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'updated_at' => Carbon::now()
            ]);
        } else {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'updated_at' => Carbon::now()
            ]);
        }
        $this->user->assignRole($this->role_ids);
        $this->reset();
        $this->closeModal();
        $this->alert('success', 'User Successfully Updated');
    }

    public function deleteAlert($user)
    {
        $this->user = ModelsUser::find($user);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->user->name .' ?',
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
        $this->user->delete();
        $this->alert('success', 'User Succesfully Deleted');
    }

    public function cancel()
    {
        $this->reset();
    }
}
