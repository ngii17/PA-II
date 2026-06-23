<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader; // Import AliasLoader
use Illuminate\Support\Facades\Blade;

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
        Blade::directive('nikMasked', function ($expression) {
            return "<?php
                \$_nik = $expression;
                \$_role = session('user.role');
                if (\$_role === 'admin') {
                    echo htmlspecialchars(\$_nik);
                } else {
                    \$_len = strlen(\$_nik);
                    if (\$_len >= 8) {
                        echo substr(\$_nik, 0, 4) . str_repeat('*', \$_len - 8) . substr(\$_nik, -4);
                    } else {
                        echo str_repeat('*', \$_len);
                    }
                }
            ?>";
        });
    }
}