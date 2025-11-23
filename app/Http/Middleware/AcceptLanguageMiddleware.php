<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptLanguageMiddleware
{
    private array $supportedLanguages = ['en', 'nl', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $acceptedLanguage = $request->header('Accept-Language');

        $locale = $this->parseAcceptLanguage($acceptedLanguage);

        app()->setLocale($locale);

        return $next($request);
    }

    private function parseAcceptLanguage(?string $acceptLanguage): string
    {
        if (! $acceptLanguage) {
            return 'en'; // Default fallback
        }

        $languages = explode(',', $acceptLanguage);

        foreach ($languages as $language) {
            $lang = trim(explode(';', $language)[0]);

            $lang = strtolower($lang);

            if (in_array($lang, $this->supportedLanguages)) {
                return $lang;
            }

            $primaryLang = explode('_', $lang)[0];
            $primaryLang = explode('-', $primaryLang)[0];

            if (in_array($primaryLang, $this->supportedLanguages)) {
                return $primaryLang;
            }
        }

        return 'en';
    }
}
