<?php

namespace App\Livewire\Product;

use App\Models\Color as ModelsColor;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Color extends Component
{
    use LivewireAlert;
    use WithPagination;
    public $isOpen = false;
    public $name, $desc;

    protected $rules = [
        'name' => 'required|unique:colors',
    ];

    public function render()
    {
        return view('livewire.product.color', [
            'colors' =>ModelsColor::paginate(10)
        ]);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        $this->validate($this->rules);
        ModelsColor::firstOrCreate(['name' => $this->name],['desc'=>$this->desc]);
        $this->reset();
        $this->closeModal();
        $this->alert('success', 'Color Succesfully Created');
    }
}
