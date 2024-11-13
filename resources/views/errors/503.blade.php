<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Maintenance</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        /*! normalize.css v8.0.1 */
        html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}
        html {
            font-family: system-ui,-apple-system,sans-serif;
            line-height: 1.5;
        }
        .bg-gray-100 {
            background-color: #f7fafc;
        }
        .flex {
            display: flex;
        }
        .min-h-screen {
            min-height: 100vh;
        }
        .items-center {
            align-items: center;
        }
        .justify-center {
            justify-content: center;
        }
        .text-center {
            text-align: center;
        }
        .max-w-xl {
            max-width: 36rem;
        }
        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }
        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem;
        }
        .text-gray-800 {
            color: #2d3748;
        }
        .text-gray-600 {
            color: #718096;
        }
        .mt-4 {
            margin-top: 1rem;
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen items-center justify-center">
        <div class="max-w-xl mx-auto px-4 text-center">
            <h1 class="text-3xl text-gray-800">
                {{ config('mae it look betterapp.name') }}
            </h1>

            <p class="mt-4 text-gray-600">
                {{ $exception->getMessage() ?: 'We are currently updating our system.' }}
            </p>

            <p class="mt-4 text-gray-600 animate-pulse">
                We'll be back shortly...
            </p>

            @if(app()->isLocal())
            <div class="mt-4 text-gray-600">
                <small>Maintenance Mode</small>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
