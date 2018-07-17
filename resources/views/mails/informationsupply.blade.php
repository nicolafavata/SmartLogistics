@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Nuova condivisione nella tua Supply Chain

Salve {{$user['name'].' '.$user['cognome']}},

L'azienda {{$rag_soc}} ha modificato la condivisione delle informazioni della tua rete Supply Chain.<br />
    Ha deciso di condividere: # {{$messaggio}}<br />


Grazie,
{{ config('app.name') }}
@endcomponent





