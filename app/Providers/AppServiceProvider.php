<?php

namespace App\Providers;

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
        /* Sanctum::authenticateAccessTokensUsing(
            static function (PersonalAccessToken $accessToken, bool $is_valid) {
                if (!$accessToken->can('read:once')) {
                    return $is_valid; // We keep the current validation.
                }
         
                return $is_valid &&  $accessToken->created_at->gt(now()->subMinutes(30));
            }
        ); */
    }
}
