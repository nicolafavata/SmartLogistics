@component('mail::message')
    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Benvenuto in Smartlogis

Salve {{$user['name']}},

ti sei registrato nella nostra applicazione con l'email: {{$user['email']}}. <br />
Per utilizzare i nostri servizi clicca sul pulsante sottostante per verificare la tua email.

@component('mail::button', ['url' => route('verify',$user->verifyUser->token)])
Verifica
@endcomponent

Grazie,<br>
{{ config('app.name') }}
@endcomponent
