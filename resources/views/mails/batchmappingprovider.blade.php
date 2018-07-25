@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # L'operazione di caricamento del mapping di $store

    Salve,
    Ti  informiamo che l'operazione richiesta in data: {{$date}}, di caricamento massivo del mapping relativo al fornitore $store è stata eseguita.
    Sono stati caricati $up codici di mapping.

    Grazie,
    {{ config('app.name') }}
@endcomponent