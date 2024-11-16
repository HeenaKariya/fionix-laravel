<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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
    // public function boot()
    // {
    //     View::composer('*', function ($view) {
    //         if (Auth::check()) {
    //             $user = Auth::user();
    //             $overallBalance = $user->calculateBalance();
    //             $view->with('overallBalance', $overallBalance);
    //         }
    //     });
    // }
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $overallBalance = $user->calculateBalance();
                $view->with('overallBalance', $overallBalance);

                // Initialize menu items array
                $menuItems = [];

                // Add balance for supervisors
                if ($user->hasRole('supervisor')) {
                    $menuItems[] = [
                        'text' => 'Balance: ' . formatIndianCurrency($overallBalance),
                        'url' => '#',
                        'icon' => '',
                        'role' => 'supervisor', 
                    ];
                }

                // Add balance for managers
                if ($user->hasRole('manager')) {
                    $menuItems[] = [
                        'text' => 'Balance: ₹' . formatIndianCurrency($overallBalance),
                        'url' => '#',
                        'icon' => '',
                        'role' => 'manager', 
                    ];
                }

                // Add user role information for all users
                $menuItems[] = [
                    'text' => 'Role: ' . strtoupper(implode(', ', $user->getRoleNames()->toArray())),
                    'url' => '#',
                    'icon' => '',
                    'role' =>  implode(', ', $user->getRoleNames()->toArray()), 
                ];
                

                // Get the current menu configuration
                $originalMenuConfig = config('adminlte.menu');

                // Filter out existing balance items
                $filteredMenu = array_filter($originalMenuConfig, function ($item) {
                    return !(isset($item['text']) && strpos($item['text'], 'Balance: ') !== false);
                });

                // Add the new menu items
                $filteredMenu = array_merge($filteredMenu, $menuItems);

                // Update the configuration
                Config::set('adminlte.menu', $filteredMenu);
            }
        });
    }

}
