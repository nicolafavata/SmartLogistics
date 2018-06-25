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


</head>

<body onload="hideloader()">
<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-primary2">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="true" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link shadow" style="color: white;" href="{{ route('admin') }}">La mia Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link shadow" style="color: white;" data-toggle="modal" data-target="#exampleModalCenter">Aggiungi una sede <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Servizi
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('adminprofile') }}">Dati fiscali</a>
                        <a class="dropdown-item" href="{{ route('viewcompany') }}">Le sedi aziendali</a>
                        <a class="dropdown-item" href="{{ route('logobusiness') }}">Logo aziendale</a>
                        <a class="dropdown-item" href="{{ route('desc_business') }}">Descrizione</a>
                        <a class="dropdown-item" href="{{ route('contattibusiness') }}">Contatti</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            Esci
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<nav class="navbar navbar-expand-lg navbar-light  basso">
    <div class="container-fluid text-left">
        <img src="img/logo.gif" width="300" height="55" alt="logo_smartlogis">
    </div>
</nav>


<!-- Modal Add Company -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Aggiungi una sede</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form onsubmit="showloader()" method="POST" action="{{ route('addcompany') }}">
                {{ csrf_field() }}
            <div class="modal-body">
                <div class="form-check-inline">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input checked type="checkbox" name="default_azienda" value="1" aria-label="Checkbox for following text input">
                        </div>
                    </div>
                    <label class="form-check">Dati di default azienda</label>
                </div>
                <div class="form-check-inline">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input checked type="checkbox" name="default_admin" value="1" aria-label="Checkbox for following text input">
                        </div>
                    </div>
                    <label class="form-check">Dati di default amministratore</label>
                </div><br />
                <div class="form-check-inline">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="checkbox" name="extra" value="1" aria-label="Checkbox for following text input">
                        </div>
                    </div>
                    <label class="form-check">Sede estera</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="submit" class="btn btn-primary">Continua</button>
            </div>
            </form>
        </div>
    </div>
</div>




    @yield('content')


    @section('footer')
        <!-- spnner -->
        <div id="loading">
        </div>


        <!-- footer -->
        <div class="container-fluid" style="text-align: center";>
            <div class="row" style="background-color: #eeeeee; align:center;">
                <div class="col-sm-12">
                    <br />
                    <h3 class="card-text">Università degli studi "Guglielmo Marconi"</h5>
                        <div class="card-body">
                            <h4 class="card-title" style="text-align: center">Facoltà di scienze e tecnologie applicate</br>Corso di Laurea in Ingegneria Informatica</h5>
                                <h5 class="card-title" style="text-align: center">Sviluppo di un'applicazione web per la smart logistics</h5>
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
                $('div.alert').fadeOut(5000);
            });
        </script>
</body>
</html>
