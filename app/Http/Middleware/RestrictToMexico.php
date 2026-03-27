<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictToMexico
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass in testing
        if (app()->environment('testing')) {
            return $next($request);
        }

        $ip = $request->getClientIp();

        // IPs privadas
        if (
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            str_starts_with($ip, '10.') ||
            str_starts_with($ip, '192.168.') ||
            str_starts_with($ip, '192.160.') ||
            (str_starts_with($ip, '172.') && (int)explode('.', $ip)[1] >= 16 && (int)explode('.', $ip)[1] <= 31)
        ) {
            return $next($request);
        }

        $country = cache()->remember("geoip_country:$ip", now()->addDays(30), function () use ($ip) {
            try {
                // Solo consultar si la protección está habilitada
                if (!\App\Models\Configuration::get('geo_block_mexico', false)) {
                    return 'DISABLED';
                }

                $response = \Illuminate\Support\Facades\Http::timeout(3)
                    ->get("https://ip-api.com/json/{$ip}?fields=status,countryCode");

                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json('countryCode');
                }
            } catch (\Exception $e) {
                \Log::error("GeoIP error: " . $e->getMessage());
            }

            return null;
        });

        // 🔒 Fail closed (más seguro) solo si está habilitado
        if (\App\Models\Configuration::get('geo_block_mexico', false) && $country !== 'DISABLED' && $country !== 'MX') {
            abort(403, 'Acceso restringido a México');
        }

        return $next($request);
    }
}
