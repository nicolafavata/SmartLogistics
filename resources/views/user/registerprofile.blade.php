@extends('layouts.userprofile')
@section('title','Smartlogis per il cittadino')
@section('content')
    <nav class="navbar navbar-light justify-content-between" style="background-color: #91ce0f";>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Grazie&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'per esserti registrato a smart logistcs'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <hr>
    <div class="container-fluid">
        <div class="col-md-12">
            <h3 class="fucsia">Vuoi inserire il tuo profilo adesso?</h3>
        </div>
    </div>
    <div class="carousel-inner register" style="background-image: url(img/register-smart.jpg);">
        <div style="padding-top: 10px; padding-bottom: 300px;" class="container">
            <div class="row">
                <div class="col-md-12 jumbotron border bianco">
                    <div class="panel-body">
                        <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('registeruser') }}">
                            {{ csrf_field() }}
                            <div>
                                <label class="font-weight-bold">Sesso</label><br />
                                <input type="radio" id="sesso" name="sesso" checked="checked" value="M" > Uomo<br />
                                <input type="radio" id="sesso" name="sesso" value="F"> Donna<br />
                            </div><br />
                            <label class="font-weight-bold">Data di nascita</label><br />
                            <input type="date" name="nascita" id="nascita" maxlength="10"><br /><br />
                            <label class="font-weight-bold">Partita iva</label><br />
                            <input type="text" name="iva" id="iva" maxlength="11"><br /><br />
                            <label class="font-weight-bold">Codice fiscale</label><br />
                            <input type="text" name="codfis" id="iva" maxlength="16"><br /><br />
                            <label class="font-weight-bold">Telefono</label><br />
                            <input type="text" name="tel" id="tel" maxlength="16"><br /><br />
                            <label class="font-weight-bold">Cellulare</label><br />
                            <input type="text" name="cell" id="cell" maxlength="16"><br /><br />
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="nazione">
                                        Nazione
                                    </label>
                                </div>
                                <select class="custom-select" id="nazione">
                                    @foreach($nazioni as $nazione)
                                        @if(($nazione->nome_stati)=='Italia')
                                                <option selected>{{$nazione->nome_stati}}</option>
                                            @else
                                                <option>{{$nazione->nome_stati}}</option>
                                            @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Provincia</label>
                                </div>
                                <select class="custom-select" name="provincia" id="provincia" onChange="ChangeById('provincia','comuni','ajax-comuni.php?provincia=')">
                                    <option selected>Seleziona la tua provincia</option>
                                    @foreach($province as $provincia)
                                        <option value={{$provincia->provincia}}>{{$provincia->provincia}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="comuni" class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Comune</label>
                                </div>
                                <select class="custom-select" name="comune">
                                    <option>Seleziona prima la provincia</option>
                                </select>
                            </div>

                            @if(Auth::user()->profile=='0')
                                <label class="font-weight-bold">Clicca su continua per accedere ai servizi</label>
                            @endif
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary" id="submit_profile">
                                        Continua
                                </button>
                            </div>

                        </form>
                    </div>
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