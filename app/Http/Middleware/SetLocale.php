<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek bahasa dari header Accept-Language
        $locale = $request->header('Accept-Language');
        
        // Cek bahasa dari parameter query
        if ($request->has('lang')) {
            $locale = $request->get('lang');
        }
        
        // Cek bahasa dari header X-Locale
        if ($request->header('X-Locale')) {
            $locale = $request->header('X-Locale');
        }

        // Validasi dan set locale
        $supportedLocales = ['id', 'en'];
        
        if ($locale) {
            // Ambil 2 karakter pertama untuk kode bahasa
            $locale = substr($locale, 0, 2);
            
            if (in_array($locale, $supportedLocales)) {
                App::setLocale($locale);
            } else {
                // Default ke bahasa Indonesia
                App::setLocale('id');
            }
        } else {
            // Default ke bahasa Indonesia jika tidak ada header
            App::setLocale('id');
        }

        return $next($request);
    }
}
