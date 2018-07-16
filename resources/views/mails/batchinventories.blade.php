@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # L'operazione di caricamento dell'inventario è stata eseguita

    Salve,
    Ti  informiamo che l'operazione richiesta in data: {{$date}}, di caricamento massivo dell'inventario è stata eseguita.
    Sono stati caricati: {{$store}} prodotti nel tuo inventario.
    Non sono stati caricati: {{$problem}} prodotti rispetto a quelli richiesti.



    Grazie,<br>
    {{ config('app.name') }}
@endcomponent