<?php

namespace App\Livewire;

use App\Models\Setting as ModelsSetting;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Setting extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $setting;
    public $name, $address, $telp, $owner, $keep_timeout, $current_logo;

    #[Validate('max:512')]
    public $logo;

    #[Title('Setting')]

    public function mount()
    {
        $this->setting = ModelsSetting::first();
        $this->name = $this->setting?->name;
        $this->address = $this->setting?->address;
        $this->current_logo = $this->setting?->logo;
        $this->telp = $this->setting?->telp;
        $this->owner = $this->setting?->owner;
        $this->keep_timeout = $this->setting?->keep_timeout;
    }
    public function render()
    {
        return view('livewire.setting');
    }

    public function save()
    {
        $this->validate();
        $path = $this->current_logo;
        if($this->logo){
            if($this->current_logo != null) {
                if (Storage::disk('public')->exists($this->current_logo)) {
                    Storage::disk('public')->delete($this->current_logo);
                }
            }
            $path = $this->logo->store('logo', 'public');
            $this->current_logo = $path;
        }
        ModelsSetting::first()->update([
            'name' => $this->name,
            'address' => $this->address,
            'telp' => $this->telp,
            'owner' => $this->owner,
            'keep_timeout' => $this->keep_timeout,
            'logo' => $path
        ]);
        $this->alert('success', 'Setting Succesfully Saved');
        $this->reset();
        $this->mount();
        try {
        } catch (\Throwable $th) {
            $this->alert('error', $th);
        }
    }

    public function deleteLogo()
    {
        $this->logo = null;
        $this->current_logo = null;
    }
}