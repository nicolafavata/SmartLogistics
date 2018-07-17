@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Diminuisce la tua rete Supply Chain

Salve {{$user['name'].' '.$user['cognome']}},

L'azienda {{$rag}} ha deciso di interrompere la condivisione delle informazioni.<br />
    Da questo momento non siete più in aggregazione Supply Chain.


Grazie,
{{ config('app.name') }}
@endcomponent
