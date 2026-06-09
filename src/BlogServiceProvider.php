<?php

namespace Devdojo\Blog;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/devdojo/blog/settings.php', 'devdojo.blog.settings');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/' => config_path('/'),
            ], 'blog:config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'blog:migrations');
        }
    }
}
