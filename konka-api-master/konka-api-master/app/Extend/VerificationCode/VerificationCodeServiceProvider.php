<?php

namespace App\Extend\VerificationCode;

use Illuminate\Support\ServiceProvider;
use Validator;

class VerificationCodeServiceProvider extends ServiceProvider
{


    public function register()
    {
        $this->app->singleton(VerificationCodeManager::class, function () {
            return new VerificationCodeManager();
        });
        $this->app->alias(VerificationCodeManager::class, 'verification_code');
    }

    public function boot()
    {
        Validator::extend('verification_code', 'App\Extend\VerificationCode\Validators\VerificationCodeValidator@validator');
        Validator::extend('verification_code_id', 'App\Extend\VerificationCode\Validators\CodeIdValidator@validator');
    }
}
