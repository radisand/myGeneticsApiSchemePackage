<?php
declare(strict_types=1);

namespace Radisand\ApiGeneralSchemeMyGenetics;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\InternalApiFetchErrorException;
use Illuminate\Support\Str;

class MyGeneticsHttpClient 
{

    /**
     * bearer token to get access
     * @var string $accessToken 
     */
    protected string $accessToken;

    /**
     * pattern for mss general url
     * @var string $patternMss
     */
    protected string $patternMss;

        
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this -> accessToken = Config::get("app.mssAccessToken");
        $this -> patternMss = Config::get("app.patternMss");
    }

         

    /**
     * multi parallel request for diff endpoints at one time
     * 
     * @param array<int,mixed[]> $requests ["method" => "", "url" => "", "data" => "", "headers" => ""]
     * @return array
     */
    public function parallelRequest(array $requests) : array
    {

        return  Http::pool(function ( $pool ) use ( &$requests ) {
           
            return array_map(function( $req ) use ( $pool ) {
                
                $method = $req['method'] ?? "get"; 
                $headers = $req['headers'] ?? [];

                return $pool -> $method(
                    $req['url'],
                    $req['data'] ?? [], 
                    [ 'headers' => [ ...$headers, ...($this -> defaultHeaders()) ] ]
                );

            }, $requests );

        });
    }



    /**
     * sendRequest for external API
     *
     * @param  string $method request method
     * @param  string $endPoint service name
     * @param  array<mixed> $data additional data with request
     * @param  array<string> $headers optional headers
     * @param callable|null $closure $closure(\Illuminate\Http\Client\Response $response) - to handle response 
     * 
     * @return mixed
     */
    public function sendRequest(string $method, string $endPoint, array $data = [], array $optionalHeaders = [], callable|null $closure = null) : mixed
    {
        
        
        $headers = !empty($optionalHeaders) 
            ? [...($this -> defaultHeaders()), ...$optionalHeaders] 
            : $this -> defaultHeaders();
        
        $stmt = Http::withHeaders($headers); 
        $endPoint = Str::replace('*' , $endPoint, $this -> getPatternMss());

        $response = match(Str::lower($method))
        {
            'get' => $stmt -> get($endPoint, $data),
            'put' => $stmt -> put($endPoint, $data),
            'delete' => $stmt -> delete($endPoint, $data),
            'patch' => $stmt -> patch($endPoint , $data),
            default => $stmt -> post($endPoint, $data),
        };
       
        $this -> validateResponse($response);


        if(!is_null($closure)){
            return $closure($response);
        }

        
        return $response -> json();
        
    }

    /**
     * Get $accessToken
     *
     * @return  string
     */ 
    protected function getAccessToken() : string
    {
        return $this -> accessToken;
    }


    /**
     * validate response 
     * @param Response
     * @throws InternalApiFetchErrorException
     * 
     * @return void
     */
    protected function validateResponse(Response $response) : void
    {
        
        if ($response -> successful() === false) 
        {
            throw new InternalApiFetchErrorException(
                $response -> body()
            );    
        }

    }



    /**
     * required mss api headers
     * 
     * @return array<string>
     */
    protected function defaultHeaders() : array
    {
        return [
            'X-Auth-Token' => $this-> getAccessToken(),
            'Accept' => 'application/json',
        ];
    }
   

    /**
     * Get $patternMss
     *
     * @return  string
     */ 
    protected function getPatternMss() : string
    {
        return $this -> patternMss;
    }
}