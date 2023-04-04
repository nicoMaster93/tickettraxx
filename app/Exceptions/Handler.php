<?php

namespace App\Exceptions;


use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if(strpos($request->getPathInfo(),"/api/") !== false){
            if($exception instanceof ModelNotFoundException){
                return response()->json(["success" => false, "error" => "Error model not found"], 400);
            }
    
            if($exception instanceof QueryException){
                return response()->json(["success" => false, "error" => "DB Error " , $exception->getMessage()], 500);
            }
    
            if($exception instanceof HttpException){
                return response()->json(["success" => false, "error" => "Route Error" , $exception->getMessage()], 404);
            }
    
            if($exception instanceof AuthenticationException){
                return response()->json(["success" => false, "error" => "Authentication Error"], 401);
            }
    
            if ($exception instanceof AuthorizationException) {
                return response()->json(["success" => false, "error" => "Authorization error, you do not have permissions"], 403);
            }

            if ($exception instanceof ValidationException) {
                $exception = $this->convertValidationExceptionToResponse($exception, $request);

                return response()->json(["success" => false, "error" => "The given data was invalid", "fields" => $exception->original['errors']], 422);
            }
        }
        
        
        return parent::render($request, $exception);
    }
}
