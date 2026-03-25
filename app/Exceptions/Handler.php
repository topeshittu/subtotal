<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use App\Utils\DemoResetHelper;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
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

    /**
     * Determine if the exception should be reported.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    public function shouldReport(Throwable $e)
    {
        if (DemoResetHelper::isResetting()) {
            if ($e instanceof QueryException || 
                $e instanceof \PDOException ||
                strpos($e->getMessage(), "doesn't exist") !== false || 
                strpos($e->getMessage(), "Base table or view not found") !== false) {
                return false;
            }
        }

        return parent::shouldReport($e);
    }

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
        // If demo data is being reset and we get ANY database error, show maintenance page
        if (DemoResetHelper::isResetting()) {
            if ($e instanceof QueryException || 
                $e instanceof \PDOException ||
                strpos($e->getMessage(), "doesn't exist") !== false || 
                strpos($e->getMessage(), "Base table or view not found") !== false ||
                strpos($e->getMessage(), "SQLSTATE") !== false) {
                
                // Get current progress message
                $progressMessage = DemoResetHelper::getProgress();
                
                // Return simple HTML response to avoid any route/view dependencies
                $html = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="refresh" content="3">
                    <title>Demo Data Reset</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; padding-top: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
                        .container { background: white; color: #333; border-radius: 20px; padding: 60px 40px; max-width: 600px; margin: 0 auto; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
                        .spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
                        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                        .status { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; color: #667eea; font-weight: 500; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="spinner"></div>
                        <h1>Demo Data Reset in Progress</h1>
                        <p>The system is being reset with fresh demo data.</p>
                        <div class="status">⏳ ' . htmlspecialchars($progressMessage) . '</div>
                        <p><small>This page will refresh automatically...</small></p>
                    </div>
                </body>
                </html>';
                
                return response($html, 503);
            }
        }

        return parent::render($request, $e);
    }
}
