@extends('layouts.template')
@section('title','La logistica intelligente')
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
                                    <a href="{{ route('login') }}"><button type="button" class="btn">Accedi</button></a>
                                    <a href="{{ route('register') }}"><button type="button" class="btn">Registrati</button></a>
                                </div>
                            </div>
                        </nav>
                    @endauth
            @endif

                <div class="carousel-inner register-business">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <li data-target="#carouselExampleIndicators" data-slide-to="0"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="2" class="active"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                                </ol>

                                <div class="carousel-inner">
                                    <div class="carousel-item" style="no-repeat center center;">
                                        <img class="d-block w-100" src="img/trova_prodotti.jpg" alt="trova_prodotti">
                                        <div class="carousel-caption d-none d-md-block animated bounceInDown">
                                            <p class="h1 stilefucsia text-left">Ti aiutiamo con gli acquisti</p>
                                            <p class="h1 stilefucsia text-left">fra i negozi della tua città</p>
                                        </div>
                                    </div>
                                    <div class="carousel-item" style="no-repeat center center;">
                                        <img class="d-block w-100" src="img/monitoraggio_magazzino.jpg" alt="monitoraggio_magazzino">
                                        <div class="carousel-caption d-none d-md-block animated bounceInDown">
                                            <p class="h1 stilefucsia text-right">Sei un azienda?</p>
                                            <p class="h1 stilefucsia text-right">Monitoriamo automaticamente le scorte del tuo magazzino</p>
                                        </div>
                                    </div>
                                    <div class="carousel-item active" style="no-repeat center center;">
                                        <img class="d-block w-100" src="img/ordini_ottimizzati.jpg" alt="ordini_ottimizzati">
                                        <div class="carousel-caption d-none d-md-block animated bounceInDown">
                                            <p class="h1 stilefucsia text-center">Ottimizza gli acquisti</p>
                                            <p class="h1 stilefucsia text-center">con le previsioni sulle vendite</p>
                                        </div>
                                    </div>
                                    <div class="carousel-item" style="no-repeat center center;">
                                        <img class="d-block w-100" src="img/ricezione_automatica_degli_ordini.jpg" alt="ricezione_automatica_degli_ordini">
                                        <div class="carousel-caption d-none d-md-block animated bounceInDown">
                                            <p class="h1 stilefucsia text-left">Noi trasmettiamo gli ordini ai tuoi fornitori</p>
                                            <p class="h1 stilefucsia text-left">tu devi solo ricevere la merce </p>
                                        </div>
                                    </div>
                                    <div class="carousel-item" style="no-repeat center center;">
                                        <img class="d-block w-100" src="img/supply_chain.jpg" alt="supply_chain">
                                        <div class="carousel-caption d-none d-md-block animated bounceInDown">
                                            <p class="h1 stilefucsia text-center">Condividi le previsioni sulle vendite</p>
                                            <p class="h1 stilefucsia text-center">con i tuoi fornitori</p>
                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
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
