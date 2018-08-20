@component('mail::message')
    <img src="https://www.nicolafavata.com/smartlogis/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
# Benvenuto in Smartlogis

Salve {{$user['name']}},

ti sei registrato nella nostra applicazione con l'email: {{$user['email']}}. <br />
Per utilizzare i nostri servizi clicca sul pulsante sottostante per verificare la tua email.

@component('mail::button', ['url' => 'https://www.nicolafavata.com/smartlogis/user/verify'.$user->verifyUser->token])
Verifica
@endcomponent

Grazie,
{{ config('app.name') }}
@endcomponent
