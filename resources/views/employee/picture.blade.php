@extends('layouts.employee')
@section('title','Smartlogis per le aziende')

@section('content_header')
    <div class="codrops-links">
        @if($dati->responsabile=='1')
            <div class="row">
                <a class="fa fa-eye" href="#"><span>&nbsp;Comuni di visibilità</span></a>
                <a class="fa fa-link" href="#"><span>&nbsp;La nostra Supply Chain</span></a>
            </div>
        @endif
        @if($dati->acquisti=='1')
            <div class="row">
                <a class="fa fa-cog" href="#"><span>&nbsp;Configurazione ordini d'acquisto</span></a>
                <a class="fa fa-cog" href="#"><span>&nbsp;Elabora ordine d'acquisto</span></a>
            </div>

        @endif
        @if($dati->produzione=='1')
            <div class="row">
                <a class="fa fa-building" href="#"><span>&nbsp;La nostra produzione</span></a>
                <a class="fa fa-cog" href="#"><span>&nbsp;Associazione acquisti-produzione</span></a>
            </div>
        @endif
        @if($dati->vendite=='1')
            <div class="row">
                <a class="fa fa-book" href="#"><span>&nbsp;Catalogo vendite</span></a>
                <a class="fa fa-money" href="#"><span>&nbsp;Le nostre vendite</span></a>
            </div>
        @endif
    </div>
@endsection

@section('content_section')
        <div class="container-fluid home_employee home_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
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
                                             @if(($dati->logo)!=NULL)
                                             src="{{'../storage/'.$dati->logo}}">
                                        @else
                                            src="{{'../img/logo_business.jpg'}}">
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