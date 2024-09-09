<?php

namespace App\Exceptions;

use App\Helpers\TelegramBot;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        $error_message = $exception->getMessage();
        $error_file = $exception->getFile();
        $error_line = $exception->getLine();
        $error_request = json_encode(Request::except(['image', 'logo']));


        if (!($exception instanceof NotFoundHttpException) && !($exception instanceof ValidationException)){
            $environment = env('APP_ENV');
            $date = date('d-m-Y H:s');
            $html = "<strong>Environment: $environment</strong>"."\n";
            $html .= "<strong>Date: ($date)</strong>"."\n";
            $html .= "<strong>File: $error_file</strong>"."\n";
            $html .= "<strong>Line: $error_line</strong>"."\n";
            $html .= "<code>Payload: $error_request</code>"."\n";
            $html .= "<code>Message: $error_message</code>"."\n";
            $html = urlencode($html);
            TelegramBot::sendHtml("$html");
            parent::report($exception);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
