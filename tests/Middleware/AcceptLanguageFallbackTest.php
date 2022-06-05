<?php

namespace PandaZoom\LaravelUserLocaleTests\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PandaZoom\LaravelUserLocale\Middleware\AcceptLanguageFallback;
use Tests\TestCase;
use function app;
use function config;

class AcceptLanguageFallbackTest extends TestCase
{
    protected const ENDPOINT_ACCEPT_LOCALE_FALLBACK = '/_test/accept-locale-fallback';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'en');
        config()->set('app.locales', ['en', 'de', 'fr']);

        Route::middleware(AcceptLanguageFallback::class)
            ->any(static::ENDPOINT_ACCEPT_LOCALE_FALLBACK, static fn(): string => 'OK');
    }

    /**
     * @dataProvider provideValidLocales
     * @param string $locale
     * @return void
     */
    public function testLocaleIsValid(string $locale): void
    {
        $request = new Request();

        $request->headers->set('Accept-Language', $locale);

        $middleware = new AcceptLanguageFallback();

        $middleware->handle($request, static fn(Request $req): string => 'OK');

        $this->assertEquals($locale, app()->getLocale());
    }

    /**
     * @dataProvider provideInvalidLocales
     * @param string $locale
     * @return void
     */
    public function testLocaleIsInvalid(string $locale): void
    {
        $request = new Request();

        $request->headers->set('Accept-Language', $locale);

        $middleware = new AcceptLanguageFallback();

        $middleware->handle($request, static fn(Request $req): string => 'OK');

        $this->assertNotEquals($locale, app()->getLocale());
    }

    protected function provideValidLocales(): iterable
    {
        yield ['en'];
        yield ['de'];
        yield ['fr'];
    }

    protected function provideInvalidLocales(): iterable
    {
        yield ['es'];
        yield ['pt'];
        yield ['ru'];
    }
}
