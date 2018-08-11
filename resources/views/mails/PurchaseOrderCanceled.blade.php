@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    # Ordine annullato

    Salve {{$name}},

    In allegato trovi il pdf con l'ordine generato in data {{$date}} relativo al fornitore {{$provider}}.
    Abbiamo annullato l'ordine perchè non rispettava i parametri forniti in fase di configurazione. Puoi sempre trasmettere l'ordine entrando nel tuo Smartlogis.


    Grazie,
    {{ config('app.name') }}
@endcomponent