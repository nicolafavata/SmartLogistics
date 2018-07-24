@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # L'operazione di caricamento delle scadenze dei prodotti è stata eseguita

    Salve,
    Ti  informiamo che l'operazione richiesta in data: {{$date}}, di caricamento massivo delle scadenze dei prodotti è stata eseguita.
    Sono state caricate le scadenze di: {{$store}} prodotti.
    Non sono state caricate le scadenze di: {{$problem}} prodotti rispetto a quelli richiesti, per alcune incongruenze nei dati inseriti.



    Grazie,
    {{ config('app.name') }}
@endcomponent