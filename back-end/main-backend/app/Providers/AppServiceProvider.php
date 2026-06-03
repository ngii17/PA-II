<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader; // Import AliasLoader

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Daftarkan Alias Excel secara manual agar bisa dikenali sistem
        $loader = AliasLoader::getInstance();
        $loader->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
    }

    public function boot(): void
    {
        //
    }
}