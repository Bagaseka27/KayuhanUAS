<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; // <- tambahkan ini
use App\Models\Karyawan; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $karyawan = Karyawan::find($user->email);

                $view->with('foto', $karyawan->FOTO ?? null);
            }
        });
    }
}