@component("emails.notifications.password.reset.layout")
@slot('title')
    <tr>
        <td>
            <table class="header" align="center" width="570" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="content-cell" align="center">

                        <h1>Уважаемый пользователь!</h1>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endslot
{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach
{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component("emails.notifications.password.reset.button", ['url' => $actionUrl, 'color' => $color])
    {{ $actionText }}
@endcomponent
@endisset
{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}
@endforeach
@slot('subcopy')
    @component("emails.notifications.password.reset.subcopy")
    @lang("Если вы испытываете трудности с кнопкой \":actionText\" скопируйте и вставте урл в браузер\n".
                ': [:actionURL](:actionURL)',
                [
                    'actionText' => $actionText,
                    'actionURL' => $actionUrl
                ]
            )
    @endcomponent
@endslot
@slot('footer')
    <tr>
        <td>
            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="content-cell" align="center">
                        © {{ date('Y') }} {{ url("/") }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endslot
@endcomponent
