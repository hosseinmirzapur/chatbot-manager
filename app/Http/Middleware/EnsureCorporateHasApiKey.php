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
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Corporate $corporate */
        $corporate = $request->route('corporate');

        if (is_null($corporate->api_key)) {
            return response()->json([
                'message' => 'دسترسی غیر مجاز'
            ], 403);
        }

        if ($corporate->status !== 'ACCEPTED') {
            return response()->json([
                'message' => 'دسترسی غیر مجاز'
            ], 403);
        }
        return $next($request);
    }
}
