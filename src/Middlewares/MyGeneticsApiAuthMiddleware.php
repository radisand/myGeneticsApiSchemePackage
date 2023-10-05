<?php
namespace Radisand\ApiGeneralSchemeMyGenetics\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\AuthServiceClientNotProvideTokenException;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\AuthServiceInvalidException;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\AuthServiceNotProvidedTokenException;
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
       
        
        /** front client vie traefik*/
        if(is_string($request -> header('X-Forwarded-By')) === true && $request -> header('X-Forwarded-By') !== "") 
        {
            if(is_null($request -> cookie("accessToken")))
            {
                throw new AuthServiceClientNotProvideTokenException('Authorization client token was not provided!');
            }

            return $next($request); 
        }

        
        /**ms token */
        if($request -> hasHeader("X-Auth-Token") === false)
        {
            throw new AuthServiceNotProvidedTokenException("X-Auth-Token token was not provided!");
        }

        $accessToken = Config::get("app.mssAccessToken");
        $incomeAuthToken = $request -> header("X-Auth-Token");


        if($accessToken !== $incomeAuthToken || $incomeAuthToken === "" || is_null($incomeAuthToken)){
            throw new AuthServiceInvalidException("The auth service token is invalid!");
        }    


        return $next($request);
    }
}