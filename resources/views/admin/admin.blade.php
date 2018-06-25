@extends('layouts.admin')
@section('title','Smartlogis per le aziende')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Salve,&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'puoi gestire le sedi della tua azienda'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner admin">
        <div class="container admin_home">
            <div class="row">
                <div class="col-md-12 jumbotron border">
                    @if(session()->has('message'))
                        @component('components.alert-success')
                            {{session()->get('message')}}
                        @endcomponent
                    @endif
                    <div class="row">
                        <div class="col-md-6 text-left align-items-center">


                                <img class="border rounded" width="240" height="188" title="logo_azienda" alt="logo_azienda"
                                     @if(($profile->logo)!=NULL)
                                        src="{{asset($profile->path)}}">
                                     @else
                                        src="{{'img/logo_business.jpg'}}">
                                     @endif
                        </div>
                        <div class="col-md-6">
                            <div class="card text-center align-items-center" style="width: 24rem;">
                                <div class="card-body">
                                    <h5 class="display-6 card-title text-uppercase font-weight-bold">{{$profile->rag_soc}}</h5>
                                    <p class="card-text">{{$profile->indirizzo}}&nbsp{{$profile->civico}}<br />
                                        @if($profile->cap_busines=='8092')
                                                {{$profile->cap_extra.' '.$profile->city.' ('.$profile->state.')'}}<br />
                                            {{$profile->nazione}}</p>
                                            @else
                                            {{$profile->cap.' '.$profile->comune.' ('.$profile->sigla_prov.')'}}</p>
                                        @endif
                                    <p class="text-uppercase">
                                        {{'Partita Iva: '.$profile->partita_iva}}<br />
                                        @if($profile->codice_fiscale)
                                            {{'Codice fiscale: '.$profile->codice_fiscale}}<br/>
                                        @endif
                                        {{'C.C.I.A.A. n.'.$profile->rea}}</p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#contatti">
                                        Contatti
                                    </button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#descrizione">
                                        Descrizione
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal contatti -->
    <div class="modal fade bd-example-modal-lg" id="contatti" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Contatti aziendali</h5>
                    <div class="modal-content">
                        <div class="row">

                                @if($profile->telefono)
                                    <div class="col-md-6 text-right shadow">
                                        <h6>Telefono:</h6>
                                    </div>
                                    <div class="col-md-6 text-left text-uppercase fucsia shadow">
                                        {{$profile->telefono}}
                                    </div>
                                @endif
                                @if($profile->cellulare)
                                    <div class="col-md-6 text-right shadow">
                                        <h6>Mobile:</h6>
                                    </div>
                                    <div class="col-md-6 text-left text-uppercase fucsia shadow">
                                        {{$profile->cellulare}}
                                    </div>
                                @endif
                                @if($profile->fax)
                                    <div class="col-md-6 text-right shadow">
                                        <h6>Fax:</h6>
                                    </div>
                                    <div class="col-md-6 text-left text-uppercase fucsia shadow">
                                        {{$profile->fax}}
                                    </div>
                                @endif
                                @if($profile->pec)
                                    <div class="col-md-6 text-right shadow">
                                        <h6>Posta certificata:</h6>
                                    </div>
                                    <div class="col-md-6 text-left text-uppercase fucsia shadow">
                                        {{$profile->pec}}
                                    </div>
                                @endif
                                    @if($profile->web)
                                        <div class="col-md-6 text-right shadow">
                                            <h6>Web-site:</h6>
                                        </div>
                                        <div class="col-md-6 text-left text-uppercase fucsia shadow">
                                            {{$profile->web}}
                                        </div>
                                    @endif

                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('contattibusiness') }}"> <button type="button" class="btn btn-secondary">Modifica</button></a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal descrizione -->
    <div class="modal fade" id="descrizione" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Descrizione azienda</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                        @if($profile->descrizione)
                            <div class="col-md-12 text-left shadow">
                                {{$profile->descrizione}}
                            </div>
                            @else
                            <div class="col-md-12 text-left shadow">
                                {{'Non è stata fornita una descrizione dell\'attività aziendale'}}
                            </div>
                        @endif

                </div>
                <div class="modal-footer">
                    <a href="{{ route('desc_business') }}"> <button type="button" class="btn btn-secondary">Modifica</button></a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
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