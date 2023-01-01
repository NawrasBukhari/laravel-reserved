<?php

namespace NawrasBukhari\Restricted;

use NawrasBukhari\Restricted\Commands\CrawlRoutes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class RestrictedServiceProvider extends ServiceProvider
{
    protected string $message = 'That :attribute is not available. Please try another!';

    protected string $fileName;

    /**
     * Publishes the config files.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/restricted.php' => config_path('restricted.php'),
        ], 'config');

        $this->fileName = config('restricted.file_path') ?: public_path('reserved.txt');
        $this->initialize();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->commands(CrawlRoutes::class);
        $this->app->register(\NawrasBukhari\Restricted\RestrictedServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['restricted'];
    }

    /**
     * @return void
     */
    public function initialize(): void
    {
        $usernames = $this->getRestrictedUsernames();

        Validator::extend('restricted', function ($attribute, $value, $parameters, $validator) use ($usernames) {
            return ! $usernames->contains($value);
        }, $this->getMessage());
    }

    /**
     * @return collection
     */
    public function getRestrictedUsernames(): collection
    {
        $path = $this->fileName;
        if (file_exists($path)) {
            $content = file_get_contents($path);

            return collect(explode(PHP_EOL, $content))->map(function ($value) {
                return preg_replace("/\s/", '', $value);
            });
        } else {
            return collect([]);
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
