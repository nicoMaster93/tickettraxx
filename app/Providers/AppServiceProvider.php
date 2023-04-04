<?php

namespace App\Providers;

use App\Models\MenuModel;
use App\Models\PermissionsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        
        view()->composer('*', function($view)
        {
            if (Auth::check()) {
                $user = Auth::user();
                $misMenus = PermissionsModel::select(["permissions.fk_menu"])
                ->where("fk_user","=",$user->id)
                ->pluck('fk_menu')->toArray();
                
                $view->with('menu_user', $misMenus);
            }
        });
        

    }
}
