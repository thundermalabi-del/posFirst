<?php

namespace App\Livewire;

use App\Models\StoreSetting;
use Livewire\Component;

class StoreSettings extends Component
{
    public $storeName = '';
    public $storeAddress = '';
    public $storePhone = '';
    public $storeEmail = '';
    public $storeDescription = '';
    public $taxRate = 0;

    public function mount()
    {
        // Load settings from database
        $settings = StoreSetting::getSettings();

        $this->storeName = $settings->name;
        $this->storeAddress = $settings->address;
        $this->storePhone = $settings->phone;
        $this->storeEmail = $settings->email ?? '';
        $this->storeDescription = $settings->description ?? '';
        $this->taxRate = $settings->tax_rate;
    }

    public function save()
    {
        $this->validate([
            'storeName' => 'required|string|max:255',
            'storeAddress' => 'required|string|max:500',
            'storePhone' => 'required|string|max:20',
            'storeEmail' => 'nullable|email|max:255',
            'storeDescription' => 'nullable|string|max:1000',
            'taxRate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Save to database
        StoreSetting::updateSettings([
            'name' => $this->storeName,
            'address' => $this->storeAddress,
            'phone' => $this->storePhone,
            'email' => $this->storeEmail,
            'description' => $this->storeDescription,
            'tax_rate' => $this->taxRate ?? 0,
        ]);

        session()->flash('success', 'Pengaturan toko berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.store-settings');
    }
}
