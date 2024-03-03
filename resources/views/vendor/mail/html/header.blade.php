@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Career Center Platform')
            <img src="" class="logo" alt="Logo">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>