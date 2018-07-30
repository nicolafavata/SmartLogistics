@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # Monitoraggio delle scadenze di magazzino

    Salve {{$name}},

    In allegato trovi il pdf con il monitoraggio delle scadenze relative ai prodotti del tuo magazzino.

    Per ricevere i prossimi avvertimenti devi cliccare l'attributo "Avvertimi" nella sezione del monitoraggio delle scadenze.

    Grazie,
    {{ config('app.name') }}
@endcomponent