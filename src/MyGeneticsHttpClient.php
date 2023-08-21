<?php
declare(strict_types=1);

namespace Radisand\ApiGeneralSchemeMyGenetics;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\InternalApiFetchErrorException;

class MyGeneticsHttpClient 
{

    /**
     * bearer token to get access
     * @var string $accessToken 
     */
    protected string $accessToken;


        
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this -> accessToken = Config::get("app.mssAccessToken");
    }

         

    /**
     * multi parallel request for diff endpoints at one time
     * 
     * @param array<string> $requests ["method" => "", "url" => "", "data" => "", "headers" => ""]
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
     * @param  string $endPoint method uri
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
        
        $response = Http::withHeaders($headers) -> send($method, "https://$endPoint", $data);

       
        $this -> validateResponse($response);


        if(!is_null($closure)){
            return $closure($response);
        }

        
        return json_decode($response -> body(), true);
        
    }

    /**
     * Get $accessToken
     *
     * @return  string
     */ 
    protected function getAccessToken() : string
    {
        return $this->accessToken;
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
        
        if ($response->successful() === false) {
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
            'Authorization' => $this-> getAccessToken(),
            'Accept' => 'application/json',
        ];
    }
   
}