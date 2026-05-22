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
use App\Repositories\Eloquent\PhongChieuRepository;
use App\Repositories\Interfaces\PhongChieuRepositoryInterface;
use App\Repositories\Eloquent\GheRepository;
use App\Repositories\Interfaces\GheRepositoryInterface;
use App\Repositories\Eloquent\XuatChieuRepository;
use App\Repositories\Interfaces\XuatChieuRepositoryInterface;
use App\Repositories\Eloquent\BapNuocRepository;
use App\Repositories\Interfaces\BapNuocRepositoryInterface;
use App\Repositories\Eloquent\GiaVeRepository;
use App\Repositories\Interfaces\GiaVeRepositoryInterface;

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
        $this->app->bind(PhongChieuRepositoryInterface::class, PhongChieuRepository::class);
        $this->app->bind(GheRepositoryInterface::class, GheRepository::class);
        $this->app->bind(XuatChieuRepositoryInterface::class, XuatChieuRepository::class);
        $this->app->bind(BapNuocRepositoryInterface::class, BapNuocRepository::class);
        $this->app->bind(GiaVeRepositoryInterface::class, GiaVeRepository::class);
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
