<?php
namespace Radisand\ApiGeneralSchemeMyGenetics;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\AuthServiceInvalidException;
use Radisand\ApiGeneralSchemeMyGenetics\Exceptions\AuthServiceNotProvidedTokenException;
use Radisand\ApiGeneralSchemeMyGenetics\MyGeneticsResponseSchemeTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class MyGeneticsApiHandler extends Handler
{

    use MyGeneticsResponseSchemeTrait;

     /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        
            $appplicationMode = config('app.env', 'production');

            /**
             * all custom application exceptions 
             * @var array $exceptions 
             */
            $exceptions = [];
            

            /**
             * @var Closure recursive search custom exceptions
             */
            $search = function (string $namespace, string $dir) use(&$exceptions, &$search){
                foreach(scandir($dir) as $segment)
                {
                    if(in_array($segment, ['..', '.'], true) === true){
                        continue 1;
                    }  

                    if(is_dir($dir.'/'.$segment) === false){
                        $exceptions[] = $namespace.'\\'.explode('.', $segment)[0];
                        continue 1;
                    }

                    $search($namespace.'/'.$segment, $dir.'/'.$segment);
                }
            };
            
            $search('App\Exceptions\Api', app_path('Exceptions/Api'));
            

            if(empty($exceptions) === false)
            {
                foreach($exceptions as $exception)
                {
                    if($e instanceof $exception)
                    {                        
                        $instanceExp = new $exception();
                        $msg = "exceptions.{$e -> getMessage()}";
                        $excMsg = $e -> getMessage();
                        
                        return $this -> responseError(
                            $instanceExp -> codeException ,
                            $msg.'.errors' === __("exceptions.$excMsg.errors") ? null : __("exceptions.$excMsg.errors"),
                            $msg === __("exceptions.$excMsg") ? null : __("exceptions.$excMsg"),
                        );

                    }
                }
            }

            return match(true)
            {
                $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
                $e instanceof NotFoundHttpException => $this -> routeNotFoundException($e, $request),
                $e instanceof AuthServiceInvalidException || $e instanceof AuthServiceNotProvidedTokenException => $this -> authMsExceptionHandler($e, $request),
                default => $appplicationMode === 'production' 
                    ? $this -> responseError(Response::HTTP_INTERNAL_SERVER_ERROR, null , 'Internal server error!') 
                    : $this -> responseError(Response::HTTP_INTERNAL_SERVER_ERROR, [
                        'line' => $e -> getLine(),
                        'file' => $e -> getFile(),
                        'trace' => $e -> getTrace(),
                    ], $e -> getMessage()), 
            };
        

    }


    /**
     * invalid auth between microservices
     * 
     * @param  AuthServiceInvalidException|AuthServiceNotProvidedTokenException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function authMsExceptionHandler(AuthServiceInvalidException|AuthServiceNotProvidedTokenException $e, $request)
    {
        return $this -> responseError(
            $e -> codeException,
            null, 
            $e -> getMessage(),
        );
    }


    /**
     * Create a response object from the given route exception.
     * @param  \Illuminate\Validation\NotFoundHttpException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function routeNotFoundException(NotFoundHttpException $e , $request)
    {
        return $this -> responseError(
            Response::HTTP_NOT_FOUND,
            null, 
            $e -> getMessage(),
        );
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return $this -> responseError(
            Response::HTTP_BAD_REQUEST, 
            $e -> errors(), 
            $e -> getMessage()
        );
    }

    
}
