<?php

namespace PandaZoom\LaravelUserLocale\Providers;

use Illuminate\Support\ServiceProvider;
use function base_path;

/**
 * @OA\Parameter(
 *  parameter="locale",
 *  name="locale",
 *  description="Locale code of the current language by iso-639-1",
 *  in="query",
 *  @OA\Schema(
 *      type="string",
 *      format="alpha",
 *      minLength=2,
 *      maxLength=2
 *    ),
 *     example="en"
 *  )
 *
 * Class AppServiceProvider
 * @package PandaZoom\LaravelUserLocale\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../../database/migrations/' => base_path('/database/migrations'),
            ], 'user-locale-migrations');
        }
    }
}
