@extends('layouts.template')
@section('title','Registrati su Smartlogis')
@section('content')
    @if (Route::has('login'))
        @auth
            <script type="text/javascript">
                window.location="home";
            </script>
        @else
            <nav class="navbar navbar-light justify-content-between" style="background-color: #ffffff";>
                <div class="container-fluid">
                    <div class="col-xs-4">
                        <div class="text-left">
                                <img src="img/logo.gif" width="300" height="55" alt="logo_smartlogis">
                        </div>
                    </div>
                    <div class="col-xs-5"></div>
                    <div class="col-xs-3 text-left">
                        <a href="{{ route('welcome') }}"><button type="button" class="btn">Home</button></a>
                        <a href="{{ route('login') }}"><button type="button" class="btn">Accedi</button></a>
                    </div>
                </div>
            </nav>
        @endauth
    @endif


    <div class="carousel-inner register-business" style="background-image: url(img/register-smart.jpg);">
        <div style="padding-top: 10px; padding-bottom: 300px;" class="container">
            <div class="row">
                <!-- Box ricerca -->
                <div class="col-md-12 jumbotron border bianco">

                <div class="panel-body">

                    <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}
                        <h1 class="display-5 registrazione">Entra anche tu in Smartlogis</h1>
                        <div class="form-group{{ $errors->has('partiva') ? ' has-error' : '' }}">
                                <div class="form-group">
                                    <div class="form-check-inline">

                                        <label class="form-check-label">
                                            Sei un azienda?
                                            <input class="form-check-input" type="radio" name="business" id="business1" value="0"
                                                @if(old('business')=='1')
                                                    onclick="lock(this, 'partiva')">
                                                @else
                                                    checked onclick="lock(this, 'partiva')">
                                                @endif
                                            <label class="form-check-label" for="exampleRadios1">
                                                No
                                            </label>
                                            <input class="form-check-input" type="radio" name="business" id="business2" value="1" onclick="unlock(this, 'partiva')"
                                                @if(old('business')=='1')
                                                    checked
                                                @endif
                                            >
                                            <label class="form-check-label" for="exampleRadios2">
                                                Si
                                            </label>
                                            <input type="text" class="form-control" name="partiva" id="partiva" maxlength="11" placeholder="Digita la partita IVA"
                                                   @if(old('business')=='1')
                                                        value="{{ old('partiva') }}">
                                                        @else
                                                        value="{{ '12345678912' }}" hidden="true">
                                                    @endif
                                                    @if(old('business')=='1')
                                                        @if ($errors->has('partiva'))
                                                            @component('components.alert-info')
                                                                {{$errors->first('partiva')}}
                                                            @endcomponent
                                                        @endif
                                                    @endif
                                        </label>
                                    </div>
                                </div>

                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name">Nome</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="Il tuo nome">
                            @if ($errors->has('name'))
                                @component('components.alert-info')
                                    {{$errors->first('name')}}
                                @endcomponent
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('cognome') ? ' has-error' : '' }}">
                            <label for="cognome">Cognome</label>
                            <input id="cognome" type="text" class="form-control" name="cognome" value="{{ old('cognome') }}" required autofocus placeholder="Il tuo cognome">
                            @if ($errors->has('cognome'))
                                @component('components.alert-info')
                                    {{$errors->first('cognome')}}
                                @endcomponent
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email">E-Mail</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="example: info@email.it">
                                @if ($errors->has('email'))
                                    @component('components.alert-info')
                                        {{$errors->first('email')}}
                                    @endcomponent
                                @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password</label>

                            <div class="form-group">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    @component('components.alert-info')
                                        {{$errors->first('password')}}
                                    @endcomponent
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">Conferma la Password</label>

                            <div class="form-group">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <input type="checkbox" name="gdpr" id="gdpr" value="1" onclick="undisabled(this, 'registrati')"> &nbsp; Confermo di aver visualizzato e accettato la Vs. Privacy Policy
                        <div class="form-group">
                            <h6 style="text-decoration: underline;" data-toggle="modal" data-target="#normativa">Privacy Policy</h6>
                        </div>
                        <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="registrati" disabled="disabled">
                                    Registrati
                                </button>
                        </div>


                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!-- Popup Normativa sulla privacy -->
    <div class="modal fade" id="normativa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Privacy policy - GDPR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Smartlogis, (in seguito, 'Titolare'), in qualità di titolare del trattamento, La informa ai sensi dell'art. 13 D.Lgs. 30.6.2003 n. 196 (in seguito, 'Codice Privacy') e dell'art. 13 Regolamento UE n. 2016/679 (in seguito, 'GDPR') che i Suoi dati saranno trattati con le modalità e per le finalità seguenti:

                    1. Oggetto del trattamento
                    Il Titolare tratta i dati personali, identificativi (ad esempio, nome, cognome, ragione sociale, indirizzo, telefono, e-mail - in seguito, 'dati personali' o anche 'dati') da Lei comunicati in occasione della conclusione di servizi offerti dal Titolare.

                    2. Finalità del trattamento
                    I Suoi dati personali sono trattati senza il Suo consenso espresso (art. 24 lett. a), b), c) Codice Privacy e art. 6 lett. b), e) GDPR), per le seguenti Finalità di Servizio:
                    - concludere i contratti per i servizi del Titolare;
                    - adempiere agli obblighi precontrattuali, contrattuali derivanti da rapporti con Lei in essere;
                    - adempiere agli obblighi previsti dalla legge, da un regolamento, dalla normativa comunitaria o da un ordine dell'Autorità (come ad esempio in materia di antiriciclaggio);
                    - esercitare i diritti del Titolare, ad esempio il diritto di difesa in giudizio.

                    3. Modalità di trattamento
                    Il trattamento dei Suoi dati personali è realizzato per mezzo delle operazioni indicate all'art. 4 Codice Privacy e all'art. 4 n. 2) GDPR e precisamente: raccolta, registrazione, organizzazione, conservazione, consultazione, elaborazione, modificazione, selezione, estrazione, raffronto, utilizzo, interconnessione, blocco, comunicazione, cancellazione e distruzione dei dati. I Suoi dati personali sono sottoposti a trattamento sia cartaceo che elettronico e/o automatizzato.
                    Il Titolare tratterà i dati personali per il tempo necessario per adempiere alle finalità di cui sopra e comunque per non oltre 10 anni dalla cessazione del rapporto per le Finalità di Servizio.

                    4. Accesso ai dati
                    I Suoi dati potranno essere resi accessibili per le finalità di cui allart. 2:
                    - a dipendenti e collaboratori del Titolare, nella loro qualità di incaricati e/o responsabili interni del trattamento e/o amministratori di sistema;
                    - a società terze o altri soggetti (a titolo indicativo, istituti di credito, studi professionali, consulenti, etc.) che svolgono attività in outsourcing per conto del Titolare, nella loro qualità di responsabili esterni del trattamento.
                    - a utenze registrate ai servizi del Titolare.

                    5. Comunicazione dei dati
                    Senza la necessità di un espresso consenso (ex art. 24 lett. a), b), d) Codice Privacy e art. 6 lett. b) e c) GDPR), il Titolare potrà comunicare i Suoi dati per le finalità di cui allart. 2 a Organismi di vigilanza (quali IVASS), Autorità giudiziarie, nonché a quei soggetti ai quali la comunicazione sia obbligatoria per legge per lespletamento delle finalità dette. Detti soggetti tratteranno i dati nella loro qualità di autonomi titolari del trattamento.
                    I Suoi dati non saranno diffusi.

                    6. Sicurezza
                    I dati vengono custoditi e controllati mediante adozione di idonee misure preventive di sicurezza, volte a ridurre al minimo i rischi di perdita e distruzione, di accesso non autorizzato, di trattamento non consentito e difforme dalle finalità per cui il trattamento viene effettuato.

                    7. Trasferimento dati
                    La gestione e la conservazione dei dati personali avverrà nel territorio dell'Unione Europea.

                    8. Diritti dellinteressato
                    Nella Sua qualità di interessato, ha il diritto di cui allart. 15 GDPR e precisamente i diritti di:
                    i. ottenere la conferma dell'esistenza o meno di dati personali che La riguardano, anche se non ancora registrati, e la loro comunicazione in forma intelligibile;
                    ii. ottenere l'indicazione: a) dell'origine dei dati personali; b) delle finalità e modalità del trattamento; c) della logica applicata in caso di trattamento effettuato con l'ausilio di strumenti elettronici; d) degli estremi identificativi del titolare, dei responsabili e del rappresentante designato ai sensi dell'art. 5, comma 2 Codice Privacy e art. 3, comma 1, GDPR; e) dei soggetti o delle categorie di soggetti ai quali i dati personali possono essere comunicati o che possono venirne a conoscenza in qualità di rappresentante designato nel territorio dello Stato, di responsabili o incaricati;
                    iii. ottenere: a) l'aggiornamento, la rettifica ovvero l'integrazione dei dati; b) la cancellazione, la trasformazione in forma anonima o il blocco dei dati trattati in violazione di legge, compresi quelli di cui non è necessaria la conservazione in relazione agli scopi per i quali i dati sono stati raccolti o successivamente trattati; c) l'attestazione che le operazioni di cui alle lettere a) e b) sono state portate a conoscenza, anche per quanto riguarda il loro contenuto, di coloro ai quali i dati sono stati comunicati o diffusi, eccettuato il caso in cui tale adempimento si rivela impossibile o comporta un impiego di mezzi manifestamente sproporzionato rispetto al diritto tutelato;
                    iv. opporsi, in tutto o in parte per motivi legittimi al trattamento dei dati personali che La riguardano, ancorché pertinenti allo scopo della raccolta.
                    Ove applicabili, Lei ha altresì i diritti di cui agli artt. 16-21 GDPR (Diritto di rettifica, diritto all'oblio, diritto di limitazione di trattamento, diritto alla portabilità dei dati, diritto di opposizione), nonché il diritto di reclamo allAutorità Garante.

                    9. Modalità di esercizio dei diritti
                    Potrà in qualsiasi momento esercitare i diritti inviando una comunicazione:
                    1. via e-mail, all'indirizzo:  gdpr@smartlogis.it

                    10. Titolare, responsabile e incaricati
                    Il Titolare del trattamento è Smartlogis.
                    L'elenco aggiornato dei responsabili e degli incaricati al trattamento è custodito ed è consultabile presso la sede del Titolare del trattamento.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footer')
    @parent

@endsection
@section('script')
    @parent

@endsection

