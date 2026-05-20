<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Eloquent\MovieRepository;
use App\Repositories\Interfaces\MovieRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Eloquent\RapChieuRepository;
use App\Repositories\Interfaces\RapChieuRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(MovieRepositoryInterface::class, MovieRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(RapChieuRepositoryInterface::class, RapChieuRepository::class);
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
