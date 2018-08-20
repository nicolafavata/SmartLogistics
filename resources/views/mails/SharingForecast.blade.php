@component('mail::message')
    <img src="https://www.nicolafavata.com/smartlogis/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # Consivisione della previsione sulle vendite

    Salve {{$name}},

    In allegato trovi il pdf con la previsione generata sulle vendite di alcuni prodotti.

    Fornitore: {{$provider}}.
    Rivenditore: {{$dealer}}.


    Grazie,
    {{ config('app.name') }}
@endcomponent