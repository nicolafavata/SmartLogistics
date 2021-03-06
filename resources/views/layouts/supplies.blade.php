<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Applicazione web per la logistica intelligente nelle Smart City">
    <meta name="author" content="Nicola Favata">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>





    <title>@yield('title','Home')</title>

    <!-- CSS di bootstrat + CSS stile personalizzato -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mystyle.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.2.0/css/font-awesome.min.css" />


</head>

<body onload="hideloader()" class="margin-top">
<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-primary2">
    <div class="container-fluid">

        <div class="profile"><img class="border-border-verde"
                                  @if ($dati->img_employee=='0')
                                  src="{{env('APP_URL')}}/img/profile.jpg"
                                  @else
                                  src="{{env('APP_URL').'/storage/'.$dati->img_employee}}"
                                  @endif
                                  width="50" height="50" alt="{{$dati->name.' '.$dati->cognome}}"/></div>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link shadow" style="color: white;" href="{{ route('employee') }}">Home <span class="sr-only">(current)</span></a>
                </li>
                @if($dati->responsabile=='1')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Dipendenti
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('viewemployees') }}">Visualizza</a>
                            <a class="dropdown-item" href="{{ route('addemployee') }}">Aggiungi</a>
                            <a class="dropdown-item" href="{{ route('upemployee') }}">Modifica</a>
                            <a class="dropdown-item" href="{{ route('delemployee') }}">Elimina</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Supply Chain
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('supplyresearch') }}">Ricerca azienda</a>
                            <a class="dropdown-item" href="{{ route('supplychainmanagement')}}">Gestione aggregazioni</a>
                            <a class="dropdown-item" href="{{route('requests-received')}}">Richieste ricevute</a>
                            <a class="dropdown-item" href="{{route('requests-transmitted')}}">Richieste trasmesse</a>
                            <a class="dropdown-item" href="{{route('block-supply')}}">Aziende bloccate</a>
                        </div>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Gestione acquisti
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{route('inventories')}}">Inventario</a>
                        <a class="dropdown-item" href="{{route('providers')}}">Fornitori</a>
                        <a class="dropdown-item" href="{{route('expires')}}">Scadenze</a>
                        <a class="dropdown-item" href="{{route('providers')}}">Configurazione</a>
                        <a class="dropdown-item" href="{{route('providers')}}">Genera ordine</a>
                        <a class="dropdown-item" href="{{route('purchase-orders')}}">Ordini effettuati</a>
                    </div>
                </li>
                @if($dati->produzione=='1')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Gestione produzione
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{route('production')}}">Catalogo della produzione</a>
                            <a class="dropdown-item" href="{{route('mapping-production')}}">Associazione acquisti-produzione</a>
                        </div>
                    </li>
                @endif
                @if($dati->vendite=='1')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Gestione vendite
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{route('catalogue')}}">Catalogo</a>
                            <a class="dropdown-item" href="{{route('add_catalogue')}}">Upload Listini</a>
                            <a class="dropdown-item" href="{{route('expire-monitor')}}">Monitoraggio delle scadenze</a>
                            <a class="dropdown-item" href="{{route('list-invoice')}}">Visualizza Fatture</a>
                            <a class="dropdown-item" href="{{route('list-sales-order')}}">Visualizza Ordini</a>
                            <a class="dropdown-item" href="{{route('desk-sales-list')}}">Visualizza Scontrini</a>
                            <a class="dropdown-item" href="{{route('new-sales-invoice')}}">Nuova Fattura</a>
                            <a class="dropdown-item" href="{{route('new-sales-order')}}">Nuovo Ordine</a>
                            <a class="dropdown-item" href="{{route('new-sales-desk')}}">Nuovo Scontrino</a>
                        </div>
                    </li>
                @endif
                <li class="nav-item active">
                    <a class="nav-link shadow" style="color: white;" href="{{ route('logout') }}"onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        Esci
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="true" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

    @yield('content')


    @section('footer')
        <!-- spnner -->
        <div id="loading">
        </div>


        <!-- footer -->
        <div class="container-fluid footer-employee">
            <div class="row">
                <div class="col-sm-12">
                    <br />
                    <h3 class="card-text">Università degli studi "Guglielmo Marconi"</h3>
                        <div class="card-body">
                            <h4 class="card-title">Facoltà di Scienze e Tecnologie Applicate</br>Corso di Laurea in Ingegneria Informatica</h4>
                            <h3 class="card-title">Sviluppo di un'applicazione web per la logistica nelle Smart City</h3>
                            <p class="card-text">Candidato: Nicola Favata - Matricola: 008272 - Relatore: Luca Regoli</p>
                        </div>
                        <div class="card-body">
                            Anno accademico 2017/2018
                        </div>
                </div>
            </div>
        </div>

    @section('script')
        <!-- file JavaScript -->
        <!-- prima jQuery, poi Popper.js, infine Bootstrap JS -->
        <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/myjs.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('div.alert').fadeOut(25000);
            });
        </script>
        @yield('ajax')
</body>
</html>
