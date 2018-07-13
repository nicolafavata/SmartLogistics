@extends('layouts.employees')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner request-supply_img">
        <br /><br /><br/><br />
        <div class="container admin_home">
            <div class="row">
                <div class="row">
                    <h1 class="fucsia font-weight-bold shadow uppercase">Le richieste di aggregazione ricevute</h1>
                </div>

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
                <div class="col-md-12 jumbotron border">
                    @forelse($company as $found)
                            <hr>
                            <div class="row">
                                <h3 class="verde font-weight-bold shadow text-uppercase">{{$found->rag_soc_company}}</h3>
                            </div>
                            <div class="row">
                                <h3 class="grigio shadow">{{'Partita Iva: '.$found->partita_iva_company}}</h3><br >
                            </div>
                            <div class="row">
                                <h5 class="grigio">{{$found->indirizzo_company.', '.$found->civico_company}}</h5>
                                <h5 class="grigio">
                                    @if($found->cap=='8092')
                                        &nbsp;{{$found->cap_company_office_extra.' '.$found->city_company_office_extra.' '.$found->state_company_office_extra.' '.$found->nazione_company}}
                                    @else
                                        &nbsp;{{$found->cap.' - '.$found->comune.' ('.$found->sigla_prov.')'}}
                                    @endif
                                </h5>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <form onsubmit="showloader()" method="POST" action="{{ route('block-company', $found->company_requested) }}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-primary2">Blocca</button>
                                    </form>
                                </div>
                                <div class="col-md-4 text-center">
                                    <form onsubmit="showloader()" method="POST" action="{{ route('cancel-company-request', $found->company_requested) }}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-primary2">Annulla</button>
                                    </form>
                                </div>
                                <div class="col-md-4 text-left">
                                    <form onsubmit="showloader()" method="POST" action="{{ route('accept-request', $found->company_requested) }}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-primary2">Accetta</button>
                                    </form>
                                </div>
                            </div>
                            <hr>
                    @empty
                        <h4 class="fucsia shadow">Non sono state trasmesse richieste di aggregazione</h4>
                    @endforelse
                            <div class="row">
                                <div class="col-md-8 push-2">
                                    {{$company->links('vendor.pagination.bootstrap-4')}}
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