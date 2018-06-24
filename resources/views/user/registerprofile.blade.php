@extends('layouts.userprofile')
@section('title','Smartlogis per il cittadino')
@section('content')
    <nav class="navbar navbar-light justify-content-between" style="background-color: #91ce0f";>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12  justify-content-between barraverde">
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
    <div class="carousel-inner register">
        <div class="container">
            <div class="row">
                <div class="col-md-12 jumbotron border bianco">
                    <div class="panel-body">
                        @if(count($errors))
                            @component('components.show-errors')
                                {{$errors}}
                            @endcomponent
                        @endif
                        <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('registeruser') }}">
                            {{ csrf_field() }}
                            <div class="table-responsive-md">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label class="font-weight-bold shadow">Sesso</label><br />
                                        </td>
                                        <td>
                                            <label class="font-weight-bold shadow">Data di nascita</label>
                                        </td>
                                        <td>
                                            <label class="font-weight-bold shadow">Codice fiscale</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="radio" id="sesso" name="sesso" checked="checked" value="M"> Uomo<br />
                                            <input type="radio" id="sesso" name="sesso" value="F"> Donna<br />
                                        </td>
                                        <td>
                                            <input type="date" name="nascita" id="nascita" maxlength="10" value="{{old('nascita')}}">
                                        </td>
                                        <td>
                                            <input class="text-uppercase" type="text" name="codice_fiscale_user_profile" id="iva" maxlength="16" placeholder="Codice fiscale" value="{{old('codice_fiscale_user_profile')}}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="font-weight-bold shadow">Telefono</label><br />
                                        </td>
                                        <td>
                                            <label class="font-weight-bold shadow">Cellulare</label>
                                        </td>
                                        <td>
                                            <label class="font-weight-bold shadow">Partita iva</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="telefono_user_profile" id="tel" maxlength="16" placeholder="Es: 091458799" value="{{old('telefono_user_profile')}}">
                                        </td>
                                        <td>
                                            <input type="text" name="cellulare_user_profile" id="cell" maxlength="16" placeholder="Es: 3487744512" value="{{old('cellulare_user_profile')}}">
                                        </td>
                                        <td>
                                            <input type="text" name="partita_iva_user_profile" id="iva" maxlength="11" placeholder="Partita IVA" value="{{old('partita_iva_user_profile')}}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="nazione">
                                                Nazione&nbsp
                                            </label>
                                        </div>
                                        <select class="custom-select" name="nazione_user_profile" id="nazione" onChange="ChangeById('nazione','ajax-comune','ajax/ajax-nazioni.php?nazione=')">
                                            @foreach($nazioni as $nazione)
                                                @if(($nazione->nome_stati)=='Italia')
                                                        <option selected value="Italia">{{$nazione->nome_stati}}</option>
                                                    @else
                                                        <option value="{{$nazione->nome_stati}}">{{$nazione->nome_stati}}</option>
                                                    @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="ajax-comune">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="inputGroupSelect01">Provincia</label>
                                            </div>
                                            <select class="custom-select" name="provincia" id="provincia" onChange="ChangeById('provincia','comuni','ajax/ajax-comuni.php?provincia=')">
                                                <option selected>Seleziona la tua provincia</option>
                                                @foreach($province as $provincia)
                                                    <option value='{{$provincia->provincia}}'>{{$provincia->provincia}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="comuni" class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="inputGroupSelect01">Comune&nbsp</label>
                                            </div>
                                            <select class="custom-select" name="comune">
                                                <option>Seleziona prima la provincia</option>
                                            </select>
                                        </div>
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