<?php
namespace Radisand\ApiGeneralSchemeMyGenetics\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\AuthServiceInvalidException;
use Symfony\Component\HttpFoundation\Response;

class MyGeneticsApiAuthMiddleware
{
     /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        
        /**if client token exists , then provide request next for client validation token*/
        if($request -> header("Authorization")){
            return $next($request); 
        }

        
        /**ms token */
        $accessToken = Config::get("app.mssAccessToken");
        $incomeAuthToken = $request -> header("X-Auth-Token");

        if($accessToken !== $incomeAuthToken && isset($incomeAuthToken)){
            throw new AuthServiceInvalidException("The auth service token is invalid!");
        }    


        return $next($request);
    }
}