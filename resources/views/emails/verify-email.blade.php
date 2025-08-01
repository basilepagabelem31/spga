@component('mail::message')
# Bonjour !

Merci de vous être inscrit(e) sur la plateforme **SPGA-SARL**.

Pour valider votre adresse e-mail et activer votre compte, veuillez cliquer sur le bouton ci-dessous.

@component('mail::button', ['url' => $actionUrl, 'color' => 'success'])
Confirmer mon adresse e-mail
@endcomponent

Si vous n'avez pas créé de compte sur notre plateforme, vous pouvez ignorer cet e-mail.

Cordialement,<br>
L'équipe de la {{ config('app.name') }}

@slot('subcopy')
{{ __('Si vous rencontrez des difficultés pour cliquer sur le bouton "Confirmer mon adresse e-mail", copiez et collez l\'URL ci-dessous dans votre navigateur web :') }}
<a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
@endslot
@endcomponent