@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # Trasmissione ordine d'acquisto

    Salve {{$name}},

    In allegato trovi il pdf con l'ordine trasmesso in data {{$date}} al fornitore {{$provider}}.


    Grazie,
    {{ config('app.name') }}
@endcomponent