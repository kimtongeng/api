<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->isFromUs()) {
            header("Access-Control-Allow-Origin: *");

            $headers = [
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'Content-type, X-Auth-Token, Authorization, Origin'
            ];
            if ($request->getMethod() === 'OPTIONS') {
                return response()->make('OK', 200, $headers);
            }

            $response = $next($request);
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        } else {
            return response()->json(['message' => $request->getMethod()], 401);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return boolean
     */
    private function isFromUs()
    {
        /* $allowSite = config('app.allow_site');
        $userAgent = request()->header('User-Agent');
        if (str_contains($userAgent, 'PostmanRuntime')) {
            return true;
        }

        if (empty($allowSite)) {
            return false;
        }
        $allowSite = explode(',', $allowSite);
        foreach ($allowSite as $obj) {
            if (str_contains(request()->header('Origin'), $obj)) {
                return true;
            }
        }
        return false; */
        return true;
    }
}
