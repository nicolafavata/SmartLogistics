@extends('layouts.employees')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner scm_img">
        <br /><br /><br/><br />
        <div class="container admin_home">
            <div class="row">
                <h1 class="verde font-weight-bold shadow">La tua Supply Chain</h1><br />
                @if(session()->has('message'))
                    @component('components.alert-success')
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
                                <h3 class="fucsia font-weight-bold shadow text-uppercase">{{$found->rag_soc_company}}</h3>
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
                                <div class="col-md-4 text-left">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal{{$found->company_received}}">
                                        Elimina
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade hide" id="exampleModal{{$found->company_received}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina aggregazione Supply Chain</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                                    <p>Eliminando l'aggregazione le condivisioni con l'azienda saranno cancellate e annullate.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$found->company_received}}">Sei sicuro?</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Modal delete -->
                                    <div class="modal fade bd-example-modal-sm hide" id="deleteModal{{$found->company_received}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content form-check">
                                                <form onsubmit="showloader()" method="POST" action="{{ route('delete-supply', $found->company_received) }}">
                                                    {{ csrf_field() }}
                                                    {{'Premi conferma per proseguire con l\'eliminazione'}}
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                        <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$found->company_received}}">Conferma</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-left">
                                    <form onsubmit="showloader()" method="GET" action="{{ route('manage-supply', $found->company_received) }}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-primary">Gestisci</button>
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