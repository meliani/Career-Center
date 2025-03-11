<x-mail::message>
# Hello {{ $user->name }},

Your password has been reset by an administrator.

Your new temporary password is:

<x-mail::panel>
{{ $password }}
</x-mail::panel>

Please log in using this temporary password and change it immediately for security purposes.

<x-mail::button :url="config('app.url')">
    Login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
