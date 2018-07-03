@extends('layouts.employee')
@section('title','Sede di '.$dati->rag_soc_company)

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' puoi aggiornare i dati della sede'}}</h2>
@endsection

@section('content_section')

    <div class="container-fluid home_employee company_img">
        <div class="row">
            <div class="col-md-12 jumbotron border employee">
                <div class="row">
                            <form onsubmit="showloader()" method="POST" action="{{ route('changemycompany') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PATCH">
                                <div class="form-row">
                                    <div class="form-check">
                                        <label class="grigio" for="">Ragione sociale</label>
                                        <input required maxlength="50" placeholder="Ex: Your Business SPA" type="text" name="rag_soc_company" id="rag_soc_company" class="font-weight-bold uppercase form-group"
                                               value="{{old('rag_soc_company', $dati->rag_soc_company)}}">
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">Partita Iva</label>
                                        <input required maxlength="11" placeholder="Partita Iva" type="text" name="partita_iva_company" id="partita_iva_company" class="font-weight-bold uppercase form-group"
                                               value="{{old('partita_iva_company', $company->partita_iva_company)}}">
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">Codice fiscale</label>
                                        <input maxlength="16" placeholder="Codice Fiscale" type="text" name="codice_fiscale_company" id="codice_fiscale_company" class="font-weight-bold uppercase form-group"
                                               value="{{old('codice_fiscale_company', $company->codice_fiscale_company)}}">
                                    </div>
                                </div>
                                @if (($dati->cap_company=='8092'))
                                    <input type="hidden" name="cap_company" value="8092">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="grigio"  for="nazione_company">
                                                Nazione&nbsp
                                            </label>
                                        </div>
                                        <select class="custom-select" name="nazione_company" id="nazione_company">
                                            @foreach($nazioni as $nazione)
                                                @if(($nazione->nome_stati)!='Italia')
                                                    <option
                                                            @if(($nazione->nome_stati==$dati->nazione_company))
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
                                            <label class="grigio" for="">Cap extra</label>
                                            <input required maxlength="8" placeholder="CAP" type="text" name="cap_company_office_extra" id="cap_company_office_extra" class="font-weight-bold text-uppercase form-group"
                                                   value="{{old('cap_company_office_extra', $dati->cap_company_office_extra)}}">
                                        </div>
                                        <div class="form-check">
                                            <label class="grigio" for="">City</label>
                                            <input required maxlength="30" placeholder="City" type="text" name="city_company_office_extra" id="city_company_office_extra" class="font-weight-bold text-uppercase form-group"
                                                   value="{{old('city_company_office_extra', $dati->city_company_office_extra)}}">
                                        </div>
                                        <div class="form-check">
                                            <label class="grigio" for="">State</label>
                                            <input required maxlength="30" placeholder="State" type="text" name="state_company_office_extra" id="state_company_office_extra" class="font-weight-bold text-uppercase form-group"
                                                   value="{{old('state_company_office_extra', $dati->state_company_office_extra)}}">
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="nazione_company" value="Italia">
                                    <div id="ajax-comune">
                                        <div class="input-group mb-3">
                                                <label class="grigio" for="inputGroupSelect01">Provincia</label>
                                            <select class="custom-select" name="provincia" id="provincia" onChange="ChangeById('provincia','comuni','ajax/employee/ajax-comuni-new-company.php?provincia=')">
                                                @foreach($province as $provincia)
                                                        <option value='{{$provincia->provincia}}'
                                                                @if ($provincia->provincia==$dati->provincia)
                                                                selected>
                                                            @else
                                                                >
                                                            @endif
                                                            {{$provincia->provincia}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="comuni">
                                                <label class="grigio" for="inputGroupSelect01">Comune&nbsp</label>
                                            <select class="custom-select" name="cap_company">
                                                @foreach($comune as $comuni)
                                                    <option value='{{$comuni->id_comune}}'
                                                            @if ($comuni->comune==$dati->comune)
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
                                        <label class="grigio" for="">Indirizzo&nbsp</label>
                                        <input required maxlength="30" placeholder="Via/Piazza" type="text" name="indirizzo_company" id="indirizzo_company" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('indirizzo_company', $dati->indirizzo_company)}}" >
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">N° civico</label>
                                        <input required maxlength="6" placeholder="Numero civico" type="text" name="civico_company" id="civico_company" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('civico_company', $dati->civico_company)}}" >
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">Telefono</label>
                                        <input maxlength="16" placeholder="065541278" type="text" name="telefono_company" id="telefono" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('telefono_company', $company->telefono_company)}}" >
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">Mobile&nbsp&nbsp&nbsp</label>
                                        <input maxlength="16" placeholder="+393884588942" type="text" name="cellulare_company" id="cellulare" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('cellulare_company', $company->cellulare_company)}}" >
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">Fax&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                        <input maxlength="16" placeholder="062255745" type="text" name="fax_company" id="fax" class="font-weight-bold text-uppercase form-group"
                                               value="{{old('fax_company', $company->fax_company)}}" >
                                    </div>
                                    <div class="form-check">
                                        <label class="grigio" for="">Email&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                        <input required maxlength="30" placeholder="example: info@email.it" type="email" name="email_company" id="email" class="font-weight-bold text-lowercase form-group"
                                               value="{{old('email_company', $company->email_company)}}" >
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                    Aggiorna
                                </button>

                    </form>
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