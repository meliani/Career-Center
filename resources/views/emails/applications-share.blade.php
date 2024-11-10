<div>
    <p>{{ __('Hello') }},</p>

    <p>{{ __('You can access the applications for the internship offer ":title" using the following link:', ['title' => $internship->project_title]) }}</p>

    <p><a href="{{ $url }}">{{ $url }}</a></p>

    <p>{{ __('This link will expire on :date.', ['date' => ($internship->expire_at ?? now()->addDays(30))->format('d/m/Y')]) }}</p>

    <p>{{ __('Best regards') }},<br>
    {{ config('app.name') }}</p>
</div>
