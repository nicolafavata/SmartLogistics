@extends('layouts.sub_admin')
@section('title','Smartlogis per le aziende')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> {{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'aggiungi una nuova sede alla tua azienda e il responsabile'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner-maxi admin_newcompany">
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
                                @foreach($busines as $business)
                        <form onsubmit="showloader()" method="POST" action="{{ route('addnewcompany') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <label class="h4 fucsia shadow font-weight-bold" for="">Sede</label><br>
                            <div class="form-row">
                                <div class="form-check">
                                <label for="">Ragione sociale</label>
                                <input required maxlength="50" placeholder="Ex: Your Business SPA" type="text" name="rag_soc_company" id="rag_soc_company" class="font-weight-bold text-uppercase form-group"
                                       @if (($default_azienda=='1'))
                                        value="{{old('rag_soc_company', $business->rag_soc)}}">
                                       @else
                                        value="{{old('rag_soc_company')}}">
                                       @endif
                                </div>
                                <div class="form-check">
                                <label for="">Partita Iva</label>
                                <input required maxlength="11" placeholder="Partita Iva" type="text" name="partita_iva_company" id="partita_iva_company" class="font-weight-bold text-uppercase form-group"
                                       @if ($default_azienda=='1')
                                        value="{{old('partita_iva_company', $business->partita_iva)}}">
                                        @else
                                            value="{{old('partita_iva_company')}}">
                                        @endif
                                </div>
                                <div class="form-check">
                                <label for="">Codice fiscale</label>
                                <input maxlength="16" placeholder="Codice Fiscale" type="text" name="codice_fiscale_company" id="codice_fiscale_company" class="font-weight-bold text-uppercase form-group"
                                    @if ($default_azienda=='1')
                                        value="{{old('codice_fiscale_company', $business->codice_fiscale)}}">
                                    @else
                                        value="{{old('codice_fiscale_company')}}">
                                    @endif
                                </div>
                            </div>
                                @if ((($default_azienda=='1') and ($business->cap_busines=='8092')) or (($default_azienda=='0') and ($extra)=='1'))
                                    <input type="hidden" name="cap_company" value="8092">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="h6 input-group-text" for="nazione_company">
                                                Nazione&nbsp
                                            </label>
                                        </div>
                                        <select class="custom-select" name="nazione_company" id="nazione_company">
                                            @foreach($nazioni as $nazione)
                                                @if(($nazione->nome_stati)!='Italia')
                                                    <option
                                                            @if(($default_azienda=='1') and ($nazione->nome_stati==$business->nazione))
                                                            selected
                                                            @endif
                                                            value="{{$nazione->nome_stati}}">{{$nazione->nome_stati}}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-check">
                                        <label for="">Cap extra</label>
                                        <input required maxlength="8" placeholder="CAP" type="text" name="cap_company_office_extra" id="cap_company_office_extra" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                                    value="{{old('cap_company_office_extra', $business->cap_extra)}}">
                                                @else
                                                    value="{{old('cap_company_office_extra')}}">
                                                @endif
                                        </div>
                                        <div class="form-check">
                                        <label for="">City</label>
                                        <input required maxlength="30" placeholder="City" type="text" name="city_company_office_extra" id="city_company_office_extra" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                                    value="{{old('city_company_office_extra', $business->city)}}">
                                                @else
                                                    value="{{old('city_company_office_extra')}}">
                                                @endif
                                        </div>
                                        <div class="form-check">
                                        <label for="">State</label>
                                        <input required maxlength="30" placeholder="State" type="text" name="state_company_office_extra" id="state_company_office_extra" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                                    value="{{old('state_company_office_extra', $business->state)}}">
                                                @else
                                                    value="{{old('state_company_office_extra')}}">
                                                @endif
                                        </div>
                                    </div>
                                @endif

                                @if ((($default_azienda=='1') and ($business->cap_busines!='8092')) or (($default_azienda=='0') and ($extra)=='0'))
                                    <input type="hidden" name="nazione_company" value="Italia">
                                    <div id="ajax-comune">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <label class="h6 input-group-text" for="inputGroupSelect01">Provincia</label>
                                                </div>
                                                <select class="custom-select" name="provincia" id="provincia" onChange="ChangeById('provincia','comuni','../ajax/ajax-comuni-new-company.php?provincia=')">
                                                    @foreach($province as $provincia)
                                                        @if($provincia->provincia!='')
                                                            <option value='{{$provincia->provincia}}'
                                                                    @if ($default_azienda=='1' and $provincia->provincia==$business->provincia)
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
                                                <select class="custom-select" name="cap_company">
                                                    @foreach($comune as $comuni)
                                                        <option value='{{$comuni->id_comune}}'
                                                                @if ($default_azienda=='1' and $comuni->comune==$business->comune)
                                                                selected>
                                                            @endif
                                                            {{$comuni->comune}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    </div>
                                @endif
                                <div class="form-row">
                                    <div class="form-check">
                                        <label for="">Indirizzo</label>
                                        <input required maxlength="30" placeholder="Via/Piazza" type="text" name="indirizzo_company" id="indirizzo_company" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                               value="{{old('indirizzo_company', $business->indirizzo)}}" >
                                        @else
                                            value="{{old('indirizzo_company')}}">
                                        @endif
                                    </div>
                                    <div class="form-check">
                                        <label for="">N° civico</label>
                                        <input required maxlength="6" placeholder="Numero civico" type="text" name="civico_company" id="civico_company" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                               value="{{old('civico_company', $business->civico)}}" >
                                        @else
                                            value="{{old('civico_company')}}">
                                        @endif
                                    </div>
                                    <div class="form-check">
                                    <label for="">Telefono</label>
                                    <input maxlength="16" placeholder="065541278" type="text" name="telefono_company" id="telefono" class="font-weight-bold text-uppercase form-group"
                                           @if ($default_azienda=='1')
                                                value="{{old('telefono_company', $business->telefono)}}" >
                                            @else
                                                value="{{old('telefono_company')}}">
                                            @endif
                                    </div>
                                    <div class="form-check">
                                        <label for="">Mobile&nbsp&nbsp&nbsp</label>
                                        <input maxlength="16" placeholder="+393884588942" type="text" name="cellulare_company" id="cellulare" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                                    value="{{old('cellulare_company', $business->telefono)}}" >
                                                @else
                                                    value="{{old('cellulare_company')}}">
                                                @endif
                                    </div>
                                    <div class="form-check">
                                        <label for="">Fax&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                        <input maxlength="16" placeholder="062255745" type="text" name="fax_company" id="fax" class="font-weight-bold text-uppercase form-group"
                                               @if ($default_azienda=='1')
                                                    value="{{old('fax_company', $business->telefono)}}" >
                                                @else
                                                    value="{{old('fax_company')}}">
                                                @endif
                                                @foreach($admi as $admin)
                                    </div>
                                    <div class="form-check">
                                        <label for="">Email&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                        <input required maxlength="30" placeholder="example: info@email.it" type="email" name="email_company" id="email" class="font-weight-bold text-lowercase form-group"
                                               @if ($default_azienda=='1')
                                                    value="{{old('email_company', $admin->email)}}" >
                                                @else
                                                    value="{{old('email_company')}}">
                                                @endif
                                    </div>
                                </div>
                                <!-- Modal categorie -->
                                <button type="button" class="btn verde" data-toggle="modal" data-target="#exampleModalLong">
                                    Seleziona le categorie merceologiche</button>

                                <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">

                                                <div class="form-row">
                                                    <label class="h4 form-check-inline fucsia shadow font-weight-bold" for="">Categorie</label><br>
                                                    @foreach($categorie as $categoria)
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <input type="checkbox" name="categoria[]" value="{{$categoria->id_categoria}}" aria-label="Checkbox for following text input">
                                                                </div>
                                                            </div>
                                                            <label class="form-check">{{$categoria->categoria}}</label>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Continua</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <label class="h4 fucsia shadow font-weight-bold" for="">Responsabile</label><br>
                                <div class="form-row">
                                    <div class="form-check">
                                        <label for="">Nome</label>
                                        <input required maxlength="255" placeholder="Nome del responsabile" type="text" name="name" id="name" class="font-weight-bold text-capitalize form-group"
                                               @if ($default_admin=='1')
                                               value="{{old('name', $admin->name)}}" >
                                        @else
                                            value="{{old('name')}}">
                                        @endif
                                    </div>
                                    <div class="form-check">
                                        <label for="">Cognome</label>
                                        <input required maxlength="255" placeholder="Cognome del responsabile" type="text" name="cognome" id="cognome" class="font-weight-bold text-capitalize form-group"
                                               @if ($default_admin=='1')
                                               value="{{old('cognome', $admin->cognome)}}" >
                                        @else
                                            value="{{old('cognome')}}">
                                        @endif
                                    </div>
                                    <div class="form-check">
                                        <label for="">Matricola</label>
                                        <input maxlength="16" placeholder="Identificativo" type="text" name="matricola" id="matricola" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('matricola')}}" >
                                    </div>
                                    <div class="form-check">
                                        <label for="">Telefono</label>
                                        <input maxlength="16" placeholder="0277841256" type="text" name="tel_employee" id="tel_employee" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('tel_employee')}}" >
                                    </div>
                                    <div class="form-check">
                                        <label for="">Mobile</label>
                                        <input placeholder="+393485517452" type="text" name="cell_employee" id="cell_employee" class="font-weight-bold text-uppercase form-group"
                                               maxlength="16" value="{{old('cell_employee')}}" >
                                    </div>

                                </div>
                                <div class="form-check">
                                    <label >Foto del profilo</label>
                                    <input type="file"  name="img_employee" id="img_employee" class="form-control">
                                </div>
                                <br />
                                <div class="form-row">
                                    <div class="form-check{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email">E-Mail</label>
                                        <input required maxlength="255" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"  placeholder="example: info@email.it">
                                        @if ($errors->has('email'))
                                            @component('components.alert-info')
                                                {{$errors->first('email')}}
                                            @endcomponent
                                        @endif
                                    </div>
                                    <div class="form-check{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password">Password</label>

                                        <div class="form-group">
                                            <input maxlength="255" id="password" type="password" class="form-control" name="password" required>

                                            @if ($errors->has('password'))
                                                @component('components.alert-info')
                                                    {{$errors->first('password')}}
                                                @endcomponent
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <label for="password-confirm">Conferma la Password</label>

                                        <div class="form-group">
                                            <input maxlength="255" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>
                                <label class="h6 form-check shadow font-weight-bold" for="">Attenzione! L'email del responsabile non può corrispondere a quella utilizzata in fase di registrazione</label><br>
                                <hr>
                                    @endforeach
                                @endforeach

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