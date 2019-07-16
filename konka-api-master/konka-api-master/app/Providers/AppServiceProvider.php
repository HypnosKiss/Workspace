<?php

namespace App\Providers;

use DB;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function (QueryExecuted $query) {
            logs()->info('Run Sql', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        });

        Validator::extend('not_exists', 'App\Validators\NotExists@validator');
        Validator::extend('check_in_specification_codes', 'App\Validators\CheckInSpecificationCodes@validator');
        Validator::extend('zh_mobile', 'App\Validators\MobileValidator@validator');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
