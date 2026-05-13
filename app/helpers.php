<?php

if (!function_exists('store_info')) {
    /**
     * Get store information with fallback defaults
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function store_info($key = null, $default = null)
    {
        $storeInfo = [
            'name' => session('store.name', 'TOKO SAYA'),
            'address' => session('store.address', 'Jl. Contoh No. 123, Kota'),
            'phone' => session('store.phone', '0812-3456-7890'),
        ];

        if ($key === null) {
            return $storeInfo;
        }

        return $storeInfo[$key] ?? $default;
    }
}
