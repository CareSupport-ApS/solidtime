<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetOrganizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log::info('SetOrganizationMiddleware triggered');
        $domain = $request->getHost();

        $organization = Organization::where('domain', $domain)->first();
        if ($organization) {
            $config = [
                'client_id' => $organization->client_id,
                'client_secret' => $organization->secret,
                'redirect' => $organization->redirect_path,
                'tenant' => $organization->tenant_id ?? null,
            ];
            config(['services.azure' => $config]);
            config(['auth.main_organization_id' => $organization->id]);
            // Log::info('Organization set to ID: '.$organization->id);
        }else{
            // Log::info("no organization found for domain: $domain");
        }
        // Log::info('Azure config: ', config('services.azure'));
        // Log::info('Main organization ID: '.config('auth.main_organization_id'));
        return $next($request);
    }
}
