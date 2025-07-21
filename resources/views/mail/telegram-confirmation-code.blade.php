<x-mail::message>

<p>Ваш код подтверждения: <strong>{{ $code }}</strong></p>
<p>Введите его в чате с ботом.</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
