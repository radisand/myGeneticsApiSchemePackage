<?php
declare(strict_types=1);

namespace Radisand\ApiGeneralSchemeMyGenetics;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

trait MyGeneticsResponseSchemeTrait 
{

    /**
     * optional headers for response
     * @var array<string>|array[] $headers
     */
    protected ?array $headers = [];


    /**
     * @var string|null $message response message
     */
    protected ?string $message = null;


    /**
     * @var null|float|int|bool|string|array|object  $data data with response 
     */
    protected null|float|int|bool|string|array|object $data = null;
    
    
    /**
     * @var array|string|null $errors list of errors
     */
    protected array|string|null $errors = null;

    /**
     * @var int $statusCode response code
     */
    protected int $statusCode;

    /**
     * @var string $status (value - error or success)
     */
    protected string $status;
    
        

    /**
     * general success response
     *
     * @param  int $successCode
     * @param  null|float|int|bool|string|array|object $data
     * @param  string $message
     * @return JsonResponse
     * 
     * @throws InvalidArgumentException
     */
    public function responseOk(
        int $successCode = Response::HTTP_OK, 
        null|float|int|bool|string|array|object $data = null, 
        string $message = null,
    ) : JsonResponse
    {

        if(is_object($data) && $data instanceof JsonResource === false) {
            throw new InvalidArgumentException("You can pass only instanse of `JsonResource` as object"); 
        }

       

        return $this 
              -> validateCodeGroup('2|3' , $successCode)
              -> setStatus("success") 
              -> setStatusCode($successCode) 
              -> setData($data) 
              -> setMessage($message) 
              -> apiResponse();

    }

    

    
    /**
     * general error response
     *
     * @param int $errorCode
     * @param array|string|null $errors
     * @return JsonResponse
     */
    public function responseError(
        int $errorCode = Response::HTTP_BAD_REQUEST, 
        array|string|null $errors = null, 
        string $message = null
    ) : JsonResponse
    {

    
        return $this 
        -> validateCodeGroup('4|5' , $errorCode)
        -> setStatus("error") 
        -> setStatusCode($errorCode) 
        -> setErrors($errors) 
        -> setMessage($message) 
        -> apiResponse();
    
    
    }



     /**
     * Set $headers to response
     *
     * @param  array<string>  $headers  $headers
     *
     * @return  self
     */ 
    public function setHeaders(array $headers) : self
    {
        $this->headers = $headers;

        return $this;
    }



    /**
     * validateCodeGroup
     *
     * @param  string $codeGroup format example: 2|3 (means. 200 and 300 code group accessed)
     * @param  int $code
     * @return self
     */
    protected function validateCodeGroup(string $codeGroup, int $code) : self
    {
        $strCode = (string) $code;

        if(in_array($strCode[0] , explode("|", $codeGroup), true) === false ){
            throw new InvalidArgumentException("The response code ($strCode) is not compatible with current method");    
        }

        return $this;
    }
  

   
    /**
     * setting config and format response 
     * 
     * @return JsonResponse
     */
    protected function apiResponse(): JsonResponse
    {
        $config = Config::get("myGeneticsApiScheme");

        $defaultData = [
            $config["key_status_code"] => $this -> statusCode,
            $config["key_status"] => $this -> status,
        ];

        if(!is_null($this -> message)) $defaultData[$config["key_message"]] = $this -> message;
        if(!is_null($this -> data)) $defaultData[$config["key_data"]] = $this -> data;
        if(!is_null($this -> errors)) $defaultData[$config["key_errors"]] = $this -> errors;
      


        return response() -> json(
            $defaultData, 
            $this -> statusCode, 
            $this -> headers
        );


    }







    /**
     * Get $message 
     *
     * @return  string|null
     */ 
    protected function getMessage() : string|null
    {
        return $this->message;
    }

    /**
     * Set $message 
     *
     * @param  string|null  $message  
     *
     * @return  self
     */ 
    protected function setMessage($message) : self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get $data data with response
     *
     * @return  null|float|int|bool|string|array|object
     */ 
    protected function getData() : null|float|int|bool|string|array|object
    {
        return $this->data;
    }

    /**
     * Set $data data with response
     *
     * @param  null|float|int|bool|string|array|object  $data  $data data with response
     *
     * @return  self
     */ 
    protected function setData($data) : self
    {
        $this->data = $data;

        return $this;
    }

    

    /**
     * Get $errors list of errors
     *
     * @return  array|string|null
     */ 
    protected function getErrors() : array|string|null
    {
        return $this->errors;
    }

    /**
     * Set $errors list of errors
     *
     * @param  array|string|null  $errors  $errors list of errors
     *
     * @return  self
     */ 
    protected function setErrors($errors) : self
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get $statusCode response code
     *
     * @return  int
     */ 
    protected function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Set $statusCode response code
     *
     * @param  int  $statusCode  $statusCode response code
     *
     * @return  self
     */ 
    protected function setStatusCode(int $statusCode) : self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Get $status (value - error or success)
     *
     * @return  string
     */ 
    protected function getStatus() : string
    {
        return $this->status;
    }

    /**
     * Set $status (value - error or success)
     *
     * @param  string  $status  $status (value - error or success)
     *
     * @return  self
     */ 
    protected function setStatus(string $status) : self
    {
        $this->status = $status;

        return $this;
    }

 
}