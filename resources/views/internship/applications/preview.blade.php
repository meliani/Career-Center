<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Applications for') }}: {{ $internship->project_title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Add logo section -->
            <div class="flex justify-center mb-8">
                <img src="{{ asset('svg/logo_entreprises_vectorized.svg') }}" alt="INPT Entreprises" class="h-16">
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">{{ __('Applications for') }}: {{ $internship->project_title }}</h1>
                <a href="{{ URL::temporarySignedRoute(
                    'internship.applications.export',
                    now()->addDays(7),
                    ['internship' => $internship->id]
                ) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500">
                    {{ __('Export to Excel') }}
                </a>
            </div>

            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Level') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Phone') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('CV') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Cover Letter') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($applications as $application)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->student->level->getLabel() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->student->phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($application->student->cv)
                                        <a href="{{ $application->student->cv }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ __('View CV') }}</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($application->student->lm)
                                        <a href="{{ $application->student->lm }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ __('View Cover Letter') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
