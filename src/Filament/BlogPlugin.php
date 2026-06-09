<?php

namespace Devdojo\Blog\Filament;

use Devdojo\Blog\Filament\Resources\Categories\CategoryResource;
use Devdojo\Blog\Filament\Resources\Posts\PostResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * Registers the blog admin resources (Posts + Categories) into a host panel:
 *
 *     ->plugin(\Devdojo\Blog\Filament\BlogPlugin::make())
 */
class BlogPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'devdojo-blog';
    }

    public function register(Panel $panel): void
    {
        if (! config('foundation.features.blog', true)) {
            return;
        }

        $panel->resources([
            PostResource::class,
            CategoryResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
