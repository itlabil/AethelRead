<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings.
     * Add new repository bindings here as they are created.
     */
    private array $repositories = [
        \App\Repositories\Contracts\NovelRepositoryInterface::class       => \App\Repositories\Eloquent\NovelRepository::class,
        \App\Repositories\Contracts\EntityRepositoryInterface::class      => \App\Repositories\Eloquent\EntityRepository::class,
        \App\Repositories\Contracts\AliasRepositoryInterface::class       => \App\Repositories\Eloquent\AliasRepository::class,
        \App\Repositories\Contracts\KeywordRepositoryInterface::class     => \App\Repositories\Eloquent\KeywordRepository::class,
        \App\Repositories\Contracts\DescriptionRepositoryInterface::class => \App\Repositories\Eloquent\DescriptionRepository::class,
        \App\Repositories\Contracts\ImageRepositoryInterface::class       => \App\Repositories\Eloquent\ImageRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositories as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Strict mode untuk mencegah lazy loading dan query berbahaya
        Model::shouldBeStrict(app()->isLocal());

        // Hindari fitur vendor-specific, gunakan standard SQL
        DB::prohibitDestructiveCommands(app()->isProduction());
    }
}
