<?php

namespace App\Http\Middleware;

use App\Models\Corporate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorporateHasApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, Corporate $corporate): Response
    {
        if (is_null($corporate->api_key)) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }
        return $next($request);
    }
}
