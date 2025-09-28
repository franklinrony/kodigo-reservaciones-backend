<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpFoundation\Response;

class JWTRefreshMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Intentar obtener el token
            $token = JWTAuth::getToken();
            
            if (!$token) {
                return response()->json(['message' => 'Token not provided'], 401);
            }

            // Intentar parsear el token (esto puede fallar si está mal formado)
            $payload = JWTAuth::getPayload($token);
            
            // Verificar si el token está dentro del período de refresh
            $now = time();
            $exp = $payload->get('exp');
            $refreshTtl = config('jwt.refresh_ttl', 20160); // 2 semanas en minutos
            
            // Calcular cuándo expira el período de refresh
            $refreshExpiresAt = $payload->get('iat') + ($refreshTtl * 60);
            
            if ($now > $refreshExpiresAt) {
                return response()->json(['message' => 'Token is outside refresh period'], 401);
            }
            
            // Si llega aquí, el token está dentro del período de refresh
            // Establecer el token en la request para que esté disponible en el controller
            JWTAuth::setToken($token);
            
        } catch (TokenExpiredException $e) {
            // Token expirado pero podría estar dentro del período de refresh
            // Intentar obtener el payload del token expirado
            try {
                $token = JWTAuth::getToken();
                $payload = JWTAuth::getPayload($token, false); // false = no validar expiración
                
                $now = time();
                $exp = $payload->get('exp');
                $refreshTtl = config('jwt.refresh_ttl', 20160);
                $refreshExpiresAt = $payload->get('iat') + ($refreshTtl * 60);
                
                if ($now > $refreshExpiresAt) {
                    return response()->json(['message' => 'Token refresh period has expired'], 401);
                }
                
                // Token está dentro del período de refresh
                JWTAuth::setToken($token);
                
            } catch (JWTException $e) {
                return response()->json(['message' => 'Invalid token'], 401);
            }
            
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}