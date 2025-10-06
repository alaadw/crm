<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'ar'];
        $defaultLocale = 'en';
        
        // Get locale from session, request, or default
        $locale = $request->get('locale') 
               ?? session('locale') 
               ?? $request->getPreferredLanguage($supportedLocales) 
               ?? $defaultLocale;
        
        // Validate locale
        if (!in_array($locale, $supportedLocales)) {
            $locale = $defaultLocale;
        }
        
        // Set locale
        app()->setLocale($locale);
        session(['locale' => $locale]);
        
        return $next($request);
    }
}
