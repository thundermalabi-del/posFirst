<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'description',
        'logo_path',
        'tax_rate',
        'is_active',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the store settings (singleton pattern)
     */
    public static function getSettings()
    {
        $settings = self::where('is_active', true)->first();

        if (!$settings) {
            // Create default settings if none exist
            $settings = self::create([
                'name' => 'TOKO SAYA',
                'address' => 'Jl. Contoh No. 123, Kota',
                'phone' => '0812-3456-7890',
                'is_active' => true,
            ]);
        }

        return $settings;
    }

    /**
     * Update or create store settings
     */
    public static function updateSettings(array $data)
    {
        $settings = self::where('is_active', true)->first();

        if ($settings) {
            $settings->update($data);
        } else {
            $data['is_active'] = true;
            $settings = self::create($data);
        }

        return $settings;
    }
}
