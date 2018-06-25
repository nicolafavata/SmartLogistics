@extends('layouts.sub_admin')
@section('title','Modifica il profilo aziendale')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Salve,&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'puoi aggiornale le informazioni della tua azienda'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner admin_update">
        <div class="container admin_home">
            <div class="row">
                <div class="col-md-12 jumbotron border">
                    <div class="row">
                        @if(session()->has('message'))
                            @component('components.alert-info')
                                {{session()->get('message')}}
                            @endcomponent
                        @endif
                        @if(count($errors))
                            @component('components.show-errors')
                                {{$errors}}
                            @endcomponent
                        @endif
                        <form onsubmit="showloader()" method="POST" action="{{ route('updateprofile') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="id_business_profile" value="{{$profile->id_business_profile}}">
                            <div class="form-group">
                                <label for="">Ragione sociale</label>
                                <input required type="text" name="rag_soc" id="rag_soc" class="font-weight-bold text-uppercase form-group" value="{{old('rag_soc', $profile->rag_soc)}}" ><br />
                                <label for="">Partita Iva</label>
                                <input required type="text" name="partita_iva" id="partita_iva" class="font-weight-bold text-uppercase form-group" value="{{old('partita_iva', $profile->partita_iva)}}" >
                                <label for="">Codice fiscale</label>
                                <input type="text" name="codice_fiscale" id="codice_fiscale" class="font-weight-bold text-uppercase form-group" value="{{old('codice_fiscale', $profile->codice_fiscale)}}" ><br />
                                <label required for="">Numero iscrizione C.C.I.A.A.</label>
                                <input type="text" name="rea" id="rea" class="font-weight-bold text-uppercase form-group" value="{{old('rea', $profile->rea)}}" ><br />
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="h6 input-group-text" for="nazione">
                                            Nazione&nbsp
                                        </label>
                                    </div>
                                    <select required class="custom-select" name="nazione" id="nazione" onChange="ChangeById('nazione','ajax-comune','../ajax/ajax-nazioni-update-business.php?nazione=')">
                                        @foreach($nazioni as $nazione)
                                            <option
                                            @if(($nazione->nome_stati)==$profile->nazione)
                                                 selected
                                            @endif
                                                 value="{{$nazione->nome_stati}}">{{$nazione->nome_stati}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="ajax-comune">
                                    @if($profile->nazione=='Italia')
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="h6 input-group-text" for="inputGroupSelect01">Provincia</label>
                                            </div>
                                            <select class="custom-select" name="provincia" id="provincia" onChange="ChangeById('provincia','comuni','../ajax/ajax-comuni-update-business.php?provincia=')">
                                                @foreach($province as $provincia)
                                                    @if($provincia->provincia!='')
                                                        <option value='{{$provincia->provincia}}'
                                                        @if ($profile->provincia==$provincia->provincia)
                                                         selected>
                                                            @else
                                                                >
                                                                @endif
                                                        {{$provincia->provincia}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="comuni" class="input-group mb-3">
                                            <div class="h6input-group-prepend">
                                                <label class="h6 input-group-text" for="inputGroupSelect01">Comune&nbsp</label>
                                            </div>
                                            <select class="custom-select" name="comune">
                                                @foreach($comune as $comuni)
                                                    <option value='{{$comuni->comune}}'
                                                            @if ($profile->comune==$comuni->comune)
                                                            selected>
                                                        @endif
                                                        {{$comuni->comune}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @else
                                        <label for="">Cap extra</label>
                                        <input required type="text" name="cap_extra" id="cap_extra" class="font-weight-bold text-uppercase form-group" value="{{old('cap_extra', $profile->cap_extra)}}" >
                                        <label for="">City</label>
                                        <input required type="text" name="city" id="city" class="font-weight-bold text-uppercase form-group" value="{{old('city', $profile->city)}}" >
                                        <label for="">State</label>
                                        <input required type="text" name="state" id="state" class="font-weight-bold text-uppercase form-group" value="{{old('state', $profile->state)}}" >
                                    @endif
                                </div>
                                <label for="">Indirizzo</label>
                                <input required type="text" name="indirizzo" id="indirizzo" class="font-weight-bold text-uppercase form-group" value="{{old('indirizzo', $profile->indirizzo)}}" >
                                <label for="">Numero civico</label>
                                <input required type="text" name="civico" id="civico" class="font-weight-bold text-uppercase form-group" value="{{old('civico', $profile->civico)}}" >
                                <a class="form-group text-center btn btn-primary" href="{{ route('admin') }}" onclick="showloader()">
                                    Annulla
                                </a>
                                <button type="submit" class="form-group text-center btn btn-primary" id="submit_profile">
                                    Conferma
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