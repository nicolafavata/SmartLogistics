@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Nuova richiesta di aggregazione Supply Chain

Salve {{$user['name'].' '.$user['cognome']}},

Un'azienda vuole entrare a far parte della tua Supply Chain, entra nel tuo account per accettare o rifiutare questa richiesta.<br />
Puoi utilizzare il pulsante sottostante per entrare nell'applicazione.

@component('mail::button', ['url' => route('login')])
Scopri
@endcomponent

Grazie,<br>
{{ config('app.name') }}
@endcomponent
