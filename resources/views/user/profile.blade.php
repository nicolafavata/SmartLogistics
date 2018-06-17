@extends('layouts.user')
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
        @if(Auth::user()->profile=='0')
            <h3 class="fucsia">Vuoi inserire il tuo profilo adesso?</h3>
            @else
            <h3 class="fucsia">Il tuo profilo</h3>
        @endif
        </div>
    </div>
    <div class="carousel-inner register" style="background-image: url(img/register-smart.jpg);">
        <div style="padding-top: 10px; padding-bottom: 300px;" class="container">
            <div class="row">
                <div class="col-md-12 jumbotron border bianco">
                    <div class="panel-body">
                        <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('register') }}">
                            {{ csrf_field() }}
                            @if ($profilo)
                            @foreach($profilo as $profile)
                            <div>
                                <label class="font-weight-bold">Sesso</label><br />
                                <input type="radio" id="sesso" name="sesso" checked="checked" value="M" > Uomo<br />
                                <input type="radio" id="sesso" name="sesso"
                                       @if($profile->sesso=='F')
                                        checked="checked"
                                       @endif
                                       value="F"> Donna<br />
                            </div><br />
                            <label class="font-weight-bold">Data di nascita</label>
                            <input type="date"

                                   @if($profile->nascita!=null)
                                    value={{$profile->nascita}}
                                   @endif
                                    name="nascita" id="nascita" maxlength="10"><br /><br />

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
                            @if(Auth::user()->profile=='0')
                                <label class="font-weight-bold">Clicca su continua per accedere ai servizi</label>
                            @endif
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary" id="submit_profile">
                                    @if(Auth::user()->profile=='0')
                                        Continua
                                    @else
                                        Aggiorna il tuo profilo
                                    @endif
                                </button>
                            </div>
                            @endforeach
                                @endif
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