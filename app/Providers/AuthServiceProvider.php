<?php

namespace App\Providers;

use App\Menu;
use App\MenuCategory;
use App\Order;
use App\Policies\MenuCategoryPolicy;
use App\Policies\MenuPolicy;
use App\Policies\OrderPolicy;
use App\Restaurant;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        MenuCategory::class => MenuCategoryPolicy::class,
        Menu::class => MenuPolicy::class,
        Order::class => OrderPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('restaurant-auth', function (Restaurant $auth, Restaurant $restaurant) {
            return $auth->id == $restaurant->id;
        });
    }
}
