<?php

namespace App\Livewire;

use App\Models\Cost;
use App\Models\Expense as ModelExpense;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Expense extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;

    #[Rule("required")]
    public $cost_id, $date, $amount, $qty, $total_amount = 0, $uom;
    public $desc;

    public $expense, $costs;

    public $query = '', $perPage = 10, $sortBy = 'date', $sortDirection = 'asc';
    public $showColumns = [
        'desc' => false,
        'created_at' => false,
        'updated_at' => false,
    ];

    #[Title('Expense')]

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

    public function updatedAmount()
    {
        $this->total_amount = (int) $this->amount * (int) $this->qty;
    }

    public function updatedQty()
    {
        $this->total_amount = (int) $this->amount * (int) $this->qty;
    }

    public function render()
    {
        $this->costs = Cost::all()->pluck('name','id')->toArray();
        return view('livewire.expense', [
            'expenses' =>ModelExpense::orderBy($this->sortBy, $this->sortDirection)
                    ->join('costs', 'expenses.cost_id', 'costs.id')
                    ->where('costs.name', 'like', '%'.$this->query.'%')
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
        ModelExpense::create([
            'cost_id' => $this->cost_id,
            'desc' => $this->desc,
            'date' => $this->date,
            'amount' => $this->amount,
            'qty' => $this->qty,
            'uom' => $this->uom,
            'total_amount' => $this->total_amount,
        ]);
        $this->reset();
        $this->closeModal();
        $this->alert('success', 'Expense Succesfully Created');
    }

    public function editExpense($expense_id)
    {
        $expense = ModelExpense::find($expense_id);
        dd($expense);
        $this->expense = ModelExpense::where('id', $expense_id)->first();
        $this->cost_id = $this->expense->cost_id;
        $this->desc = $this->expense->desc;
        $this->date = $this->expense->date;
        $this->amount = $this->expense->amount;
        $this->qty = $this->expense->qty;
        $this->uom = $this->expense->uom;
        $this->total_amount = $this->expense->total_amount;
        $this->isOpen = true;
    }

    public function update() {
        $this->expense->update([
            'cost_id' => $this->cost_id,
            'desc' => $this->desc,
            'date' => $this->date,
            'amount' => $this->amount,
            'qty' => $this->qty,
            'uom' => $this->uom,
            'total_amount' => $this->total_amount,
        ]);
        $this->closeModal();
        $this->alert('success', 'Expense Successfully Updated');
    }

    public function deleteAlert($expense)
    {
        $this->expense = ModelExpense::find($expense);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this expense ?',
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
        $this->expense->delete();
        $this->alert('success', 'Expense Succesfully Deleted');
    }

    public function cancel()
    {
        $this->reset();
    }
}
