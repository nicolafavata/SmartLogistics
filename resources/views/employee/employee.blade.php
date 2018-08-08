@extends('layouts.employee')
@section('title','Smartlogis per le aziende')

@section('content_header')
    <div class="codrops-links">
        @if($dati->responsabile=='1')
            <div class="center">
                <a class="fa fa-eye" href="{{route('visiblecompany')}}"><span>&nbsp;Visibilità</span></a>
                <a class="fa fa-link" href="{{route('supplychainmanagement')}}"><span>&nbsp;Gestione Supply Chain</span></a>
            </div>
        @endif
        @if($dati->acquisti=='1')
            <div class="center">
                <a class="fa fa-shopping-cart" href="{{route('providers')}}"><span>&nbsp;Fornitori</span></a>
                <a class="fa fa-list-ol" href="#"><span>&nbsp;Ordini d'acquisto</span></a>
            </div>

        @endif
        @if($dati->produzione=='1')
            <div class="center">
                <a class="fa fa-building-o" href="{{route('production')}}"><span>&nbsp;La nostra produzione</span></a>
                <a class="fa fa-cog" href="{{route('mapping-production')}}"><span>&nbsp;Associazione acquisti-produzione</span></a>
            </div>
        @endif
        @if($dati->vendite=='1')
            <div class="center">
                <a class="fa fa-book" href="{{route('catalogue')}}"><span>&nbsp;Catalogo vendite</span></a>
                <a class="fa fa-calendar" href="{{route('expire-monitor')}}"><span>&nbsp;Monitoraggio delle scadenze</span></a>
            </div>
        @endif
    </div>
@endsection

@section('content_section')
        <div class="container-fluid home_employee home_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <div class="row">
                                    <h1 class="shadow grigio uppercase center">{{$dati->rag_soc_company}}</h1>
                                    <h2 class="shadow verdino capitalize center">{{$dati->indirizzo_company.' '.$dati->civico_company.' - '}}
                                        @if ($dati->cap=='8092')
                                            {{$dati->cap_company_office_extra.' '.$dati->city_company_office_extra.' ('.$dati->state_company_office_extra.' - '.$dati->nazione_company.')'}}
                                        @else
                                            {{$dati->cap.' '.$dati->comune.' ('.$dati->sigla_prov.')'}}
                                        @endif
                                    </h2>
                                    <div class="row form-check center">
                                        <img class="border-border-fucsia doppio img-fluid image-responsive" title="{{$dati->rag_soc_company}}" alt="Logo {{$dati->rag_soc_company}}"
                                             @if(($dati->logo)!='0')
                                             src="{{env('APP_URL').'/storage/'.$dati->logo}}">
                                        @else
                                            src="{{env('APP_URL').'/img/logo_business.jpg'}}">
                                        @endif
                                    </div>
                                    <h2 class="shadow verdino center">Clicca sul pulsante in alto a destra per accedere al menù principale</h2>


                          
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