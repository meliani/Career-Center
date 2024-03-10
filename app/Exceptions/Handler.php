<?php

namespace App\Exceptions;

use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            Log::info($e->getMessage());
        });

        $this->renderable(function (QueryException $e, Request $request) {
            $this->handleDuplicateEntryException($e);

            //  continu browsing normally and return http response
            // return $request;
        });

    }

    public function handleDuplicateEntryException(Throwable | QueryException $e)
    {
        // report($e);
        Notification::make()
            ->title('Error : ')// . $e->getMessage())
            ->danger()
            ->send();

        // return response();
        // return response()->json([
        //     'message' => 'Duplicate entry',
        //     'errors' => [
        //         'name' => 'The name has already been taken.',
        //     ],
        // ], 422);
    }
}
