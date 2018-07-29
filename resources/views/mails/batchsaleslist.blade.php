@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # L'operazione di caricamento dei listini prezzo è stata eseguita

    Salve,
    Ti  informiamo che l'operazione richiesta in data: {{$date}}, di caricamento massivo dei prezzi dei prodotti è stata eseguita.
    Sono stati caricati: {{$store}} prezzi del tuo catalogo.
    Non sono stati caricati: {{$problem}} prezzi rispetto a quelli richiesti.



    Grazie,
    {{ config('app.name') }}
@endcomponent