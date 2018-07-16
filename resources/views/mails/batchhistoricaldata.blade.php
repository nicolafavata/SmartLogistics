@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # L'operazione di caricamento dei dati storici è stata eseguita

    Salve,
    Ti  informiamo che l'operazione richiesta in data: {{$date}}, di caricamento massivo dei dati storici è stata eseguita.
    Sono state processate: {{$store}} operazioni di vendita effettuata.
    Non sono state processate: {{$problem}} operazioni di vendita rispetto a quelle richieste.



    Grazie,<br>
    {{ config('app.name') }}
@endcomponent