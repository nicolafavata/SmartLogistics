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
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mystyle.css') }}" rel="stylesheet">

    <!--script javascript -->
    <script type="text/javascript" src="js/myjs.js"></script>

</head>

<body onload="hideloader()">
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
    @yield('web_script')
    @section('script')
        <!-- file JavaScript -->
        <!-- prima jQuery, poi Popper.js, infine Bootstrap JS -->
        <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
