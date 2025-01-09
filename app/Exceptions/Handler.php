<?php

namespace App\Exceptions;

use Filament\Notifications\Notification;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $withoutDuplicates = true;

    // protected $dontReport = [
    //     QueryException::class,
    // ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('log')) {
                Log::info($e->getMessage());
            }
        });

        $this->renderable(function (TransportException $e) {
            Log::error('Mail server connection failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
            ]);

            if (request()->is('backend/*') || request()->is('*')) {
                Notification::make()
                    ->title(__('Notification Email Failed'))
                    ->body(__('Unable to send notification email at this time. Mail server connection failed. Please contact administrators: ') . config('mail.from.address'))
                    ->danger()
                    ->persistent()
                    ->sendToDatabase(auth()->user());

            }

            // return response()->noContent();

            // return response()->json([
            //     'message' => 'Mail exception handled',
            // ], 200);

            return response()->json([
                'message' => 'Unable to send email at this time. Please try again later.',
                'error' => 'mail_connection_failed',
            ], 503);
        });
    }
}
