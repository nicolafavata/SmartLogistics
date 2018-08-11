@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # Ordine generato in attesa di trasmissione

    Salve {{$name}},

    In allegato trovi il pdf con l'ordine generato in data {{$date}} relativo al fornitore {{$provider}}.
    L'ordine rispetta i parametri da te forniti in fase di configurazione, ma come richiesto non è stato ancora trasmesso.
    Entra nel tuo Smartlogis e dai conferma di trasmissione.


    Grazie,
    {{ config('app.name') }}
@endcomponent