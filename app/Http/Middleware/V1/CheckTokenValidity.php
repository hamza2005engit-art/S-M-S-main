<?php

namespace App\Http\Middleware\V1;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckTokenValidity
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    public function handle($request, Closure $next)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $payload = JWTAuth::parseToken()->getPayload();
            $issuedAt = $payload->get('iat');
            if (
                $user->password_changed_at &&
                Carbon::parse($user->password_changed_at)->timestamp > $issuedAt
            ) {
                return response()->json(['error' => 'Token expired'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
