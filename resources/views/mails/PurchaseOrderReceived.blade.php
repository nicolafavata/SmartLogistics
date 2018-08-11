@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # Ricezione nuovo ordine

    Complimenti {{$name}},

    In allegato trovi il pdf con l'ordine trasmesso in data {{$date}} dal cliente {{$provider}}.


    Grazie,
    {{ config('app.name') }}
@endcomponent