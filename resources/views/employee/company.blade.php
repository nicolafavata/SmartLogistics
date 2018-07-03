@extends('layouts.employee')
@section('title','Sede di '.$dati->rag_soc_company)

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' questi sono i dati della sede'}}</h2>
@endsection

@section('content_section')
        <div id="body_page" class="container-fluid home_employee">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                            <table class="table table-responsive">
                                <tr>
                                    <td @if($company->cellulare_company!=null)
                                            rowspan="8">
                                        @else
                                            rowspan="7">
                                        @endif
                                        <img class="border-border-fucsia doppio img-fluid image-responsive" title="{{$dati->name.' '.$dati->cognome}}" alt="Profilo {{$dati->name.' '.$dati->cognome}}"
                                             @if(($dati->logo)!=NULL)
                                             src="{{'../storage/'.$dati->logo}}">
                                        @else
                                            src="{{'../img/profile.jpg'}}">
                                        @endif
                                    </td>
                                    <td class="grigio shadow destra">
                                        Ragione sociale:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra uppercase">
                                        {{$dati->rag_soc_company}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Sede:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        {{$dati->indirizzo_company.' '.$dati->civico_company.' - '}}
                                        @if ($dati->cap_company=='8092')
                                            {{$dati->cap_company_office_extra.' '.$dati->city_company_office_extra.' ('.$dati->state_company_office_extra.' - '.$dati->nazione_company.')'}}
                                        @else
                                            {{$dati->cap.' '.$dati->comune.' ('.$dati->sigla_prov.')'}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Partita Iva:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        {{$company->partita_iva_company}}
                                    </td>
                                </tr>
                                @if($company->codice_fiscale_company!=null)
                                <tr>
                                    <td class="grigio shadow destra">
                                        Codice fiscale:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra uppercase">
                                        {{$company->codice_fiscale_company}}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="grigio shadow destra">
                                        Telefono:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        @if($company->telefono_company!=null)
                                        {{$company->telefono_company}}
                                            @else
                                        {{'Non inserito'}}
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Mobile:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        @if($company->cellulare_company!=null)
                                        {{$company->cellulare_company}}
                                        @else
                                            {{'Non inserito'}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Fax:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        @if($company->fax_company!=null)
                                            {{$company->fax_company}}
                                        @else
                                            {{'Non inserito'}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Email:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        {{$company->email_company}}
                                    </td>
                                </tr>
                            </table>
                        <br />
                        <div class="row center grigio shadow">
                            Vuoi aggiornare i dati?
                        </div><br />
                        <div class="row center">
                            <a  href="{{ route('upcompany') }}" class="form-group text-center btn btn-primary pulsante">
                                Aggiorna
                            </a>
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