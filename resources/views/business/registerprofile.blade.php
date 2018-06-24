@extends('layouts.businessprofile')
@section('title','Smartlogis per le aziende')
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
            <h3 class="display-6 shadow">Devi completare il profilo della tua azienda</h3>
        </div>
    </div>
    <div class="carousel-inner register-business">
        <div class="container">
            <div class="row">
                <div class="col-md-12 jumbotron border bianco">
                    <div class="panel-body">
                        @if(count($errors))
                            @component('components.show-errors')
                                {{$errors}}
                            @endcomponent
                        @endif
                        <form id="register-business" onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('registerbusiness') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="h6 fucsia">Ragione sociale (*)</label>
                                    </div>
                                    <div class="col-md-9 text-left">
                                        <input required class="form-check-inline text-uppercase" type="text" name="rag_soc" id="rag_soc" maxlength="50" placeholder="Denominazione" value="{{old('rag_soc')}}">
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="h6 shadow">Amministratore</label>
                                    </div>
                                    <div class="text-capitalize col-md-9 text-left">
                                        {{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="h6 shadow">Partita IVA</label>
                                    </div>
                                    <div class="col-md-9 text-left">
                                        <label>
                                            @foreach($partiva as $iva)
                                                {{$iva->partita_iva}}
                                            @endforeach
                                        </label>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="h6 fucsia">C.C.I.A.A. (*)</label>
                                    </div>
                                    <div class="col-md-9 text-left">
                                        <input required class="form-check-inline" type="text" name="rea" id="rea" maxlength="8" placeholder="N.Rea" value="{{old('rea')}}">
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="h6 shadow">Codice fiscale</label>
                                    </div>
                                    <div class="col-md-9 text-left">
                                        <input class="form-check-inline text-uppercase" type="text" name="codice_fiscale" id="codice_fiscale" maxlength="16" placeholder="Codice fiscale" value="{{old('codice_fiscale')}}">
                                    </div>
                                </div>
                                <hr>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="h6 fucsia input-group-text" for="nazione">
                                            Nazione(*)&nbsp
                                        </label>
                                    </div>
                                    <select required class="custom-select" name="nazione" id="nazione" onChange="ChangeById('nazione','ajax-comune','ajax/ajax-nazioni-business.php?nazione=')">
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
                                            <label class="h6 fucsia input-group-text" for="inputGroupSelect01">Provincia(*)</label>
                                        </div>
                                        <select class="custom-select" name="provincia" id="provincia" onChange="ChangeById('provincia','comuni','ajax/ajax-comuni-business.php?provincia=')">
                                            <option selected>Seleziona la tua provincia</option>
                                            @foreach($province as $provincia)
                                                <option value='{{$provincia->provincia}}'>{{$provincia->provincia}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="comuni" class="input-group mb-3">
                                        <div class="h6 fucsia input-group-prepend">
                                            <label class="h6 fucsia input-group-text" for="inputGroupSelect01">Comune(*)&nbsp</label>
                                        </div>
                                        <select class="custom-select" name="comune">
                                            <option>Seleziona prima la provincia</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                            <div class="table-responsive-md">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="h6 shadow">Telefono</label>
                                            </td>
                                            <td>
                                                <label class="h6 shadow">Cellulare</label>
                                            </td>
                                            <td>
                                                <label class="h6 shadow">Fax</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="telefono" id="tel" maxlength="16" placeholder="Es: 091458799" value="{{old('telefono')}}">
                                            </td>
                                            <td>
                                                <input type="text" name="cellulare" id="cell" maxlength="16" placeholder="Es: 3487744512" value="{{old('cellulare')}}">
                                            </td>
                                            <td>
                                                <input type="text" name="fax" id="iva" maxlength="11" placeholder="Es: 091458799" value="{{old('fax')}}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="h6 shadow">Email</label>
                                            </td>
                                            <td>
                                                <label class="h6 shadow">Pec</label>
                                            </td>
                                            <td>
                                                <label class="h6 shadow">Home page</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ Auth::user()->email }}
                                            </td>
                                            <td>
                                                <input type="email" name="pec" id="cell" maxlength="30" placeholder="Es: email@pec.yourbusiness.it" value="{{old('pec')}}">
                                            </td>
                                            <td>
                                                <input type="text" name="web" id="iva" maxlength="30" placeholder="www.yourbusiness.it" value="{{old('web')}}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="h6 shadow">Una breve descrizione della tua attività</label>
                                    </div>
                                    <div class="col-md-9 text-left">
                                        <textarea class="card-text" name="descrizione" form="register-business" id="rea" maxlength="255" placeholder="Descrivi la tua azienda" value="{{old('descrizione')}}"></textarea>
                                    </div>
                                </div>
                                <br />
                                <div class="form-group">
                                    <label class="h6 shadow">Seleziona un logo per la tua azienda (320x250 pixel, tipo di file: jpg / gif / png)</label>
                                    <input type="file"  name="logo" id="logo" class="form-control">

                                </div>
                                <br />


                                <label class="fucsia shadow font-weight-bold">Inserisci i dati obbligatori (*) per proseguire</label>
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