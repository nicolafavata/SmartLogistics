@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # L'operazione di caricamento dell'inventario è stata eseguita

    Prova trasmissione file



    Grazie,
    {{ config('app.name') }}
@endcomponent