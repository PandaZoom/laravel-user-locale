<?php

namespace PandaZoom\LaravelUserLocale\Middleware;

use ArrayIterator;
use Closure;
use Illuminate\Support\Str;
use function app;
use function auth;
use function config;
use function explode;
use function in_array;

class AcceptLanguageFallback
{
    public const HEADER_ACCEPT_LANGUAGE = 'Accept-Language';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        $locale = auth()?->user()?->locale;

        if ($locale === null) {

            $value = $request->header(static::HEADER_ACCEPT_LANGUAGE);

            if ($value !== null) {
                $locale = $this->validateLanguage($value);
            }
        }

        if ($locale !== null
            && in_array($locale, $this->getSupportedLocales(), true)
            && $locale !== app()->getLocale()) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function validateLanguage(string $str): ?string
    {
        /*
         * be sure to check $lang of the format "de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4"
         * this means:
         *  1) give me de-DE if it is available
         *  2) otherwise, give me de
         *  3) otherwise, give me en-US
         *  4) if all fails, give me en
        */

        // split it up by ","
        $languages = explode(',', $str);

        // we need an ArrayIterator because we will be extending the FOREACH below dynamically!
        $languageIterator = new ArrayIterator($languages);

        $supportedLanguages = $this->getSupportedLocales();

        foreach ($languageIterator as $language) {
            // split it up by ";"
            $locale = explode(';', $language);

            // now check, if this locale is "supported"
            if (in_array($locale[0], $supportedLanguages, true)) {
                return $locale[0];
            }

            // now check, if the language to be checked is in the form of de-DE
            if (Str::contains($locale[0], '-')) {
                // extract the "main" part ("de") and append it to the end of the languages to be checked
                $base = explode('-', $locale[0]);
                $languageIterator[] = $base[0];
            }
        }

        return null;
    }

    protected function getSupportedLocales(): array
    {
        return (array)config('app.locales');
    }
}
