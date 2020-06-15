<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        $this->registerPolicies();

        //KITA MEMBUAT GATE DENGAN NAMA order-view, DIMANA DIA MEMINTA DUA PARAMETER YAKNI CUSTOMER DAN ORDER
        Gate::define('order-view', function($customer, $order) {
            //KEMUDIAN DICEK, JIKA CUSTOMER ID SAMA DENGAN CUSTOMER_ID YANG ADA PADA TABLE ORDER
            //MAKA RETURN-NYA TRUE
            //GATE INI HANYA AKAN ME-RETURN TRUE/FALSE SEBAGAI TANDA DIIZINKAN ATAU TIDAK
            return $customer->id == $order->customer_id;
        });
      
        //DEFINISIKAN GATE LAINNYA DISINI JIKA PERLU
        View::composer('ecommerce.*', 'App\Http\View\CategoryComposer');
    }
}
