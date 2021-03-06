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

<body onload="hideloader()">
<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-primary2">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="true" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link shadow" style="color: white;" href="{{ route('user') }}">La mia Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link shadow" style="color: white;" href="{{ route('user') }}">Cerca un prodotto</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle shadow" href="#" style="color: white;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Servizi
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('view-profile') }}">Il mio account</a>
                        <a class="dropdown-item" href="{{ route('user') }}">Cerca un prodotto</a>
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
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/myjs.js') }}"></script>
        <script src="https://unpkg.com/axios@0.18.0/dist/axios.min.js"></script>
</body>
</html>
