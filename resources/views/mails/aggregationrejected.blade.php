@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Annullata richiesta di aggregazione Supply Chain

Salve {{$user['name'].' '.$user['cognome']}},

L'azienda {{$user['rag_soc_company']}} ha annullato la tua richiesta di aggregazione supply chain.<br />


Grazie,<br>
{{ config('app.name') }}
@endcomponent
