@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Annullata richiesta di aggregazione Supply Chain

Salve {{$user['name'].' '.$user['cognome']}},

Un'azienda ha annullato una precedente richiesta di aggregazione supply chain.<br />


Grazie,
{{ config('app.name') }}
@endcomponent
