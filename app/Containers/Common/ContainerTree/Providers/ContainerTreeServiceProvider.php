<?php

namespace App\Containers\Common\ContainerTree\Providers;

use App\Containers\Common\ContainerTree\UI\CLI\Commands\MakeContainerTree;
use Illuminate\Support\ServiceProvider;

final class ContainerTreeServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        // No migrations or config to register
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands([
            MakeContainerTree::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }
}
