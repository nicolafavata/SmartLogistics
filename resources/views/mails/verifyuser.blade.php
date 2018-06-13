@component('mail::message')
    <img class="img" src="http://localhost:8000/img/logo.gif" alt="logo">
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
