@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Una nuova azienda entra a far parte della tua rete Supply Chain

Complimenti {{$user['name'].' '.$user['cognome']}},

L'azienda {{$rag}} ha accettato di far parte della tua rete Supply Chain.<br />



Grazie,<br>
{{ config('app.name') }}
@endcomponent
