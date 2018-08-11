@extends('layouts.supplies')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner supplies_img">
        <br /><br /><br/><br />
        <div class="container admin_home">
            <div class="row">
                <div class="row">
                    <h1 class="fucsia font-weight-bold shadow uppercase">I tuoi fornitori</h1>
                </div><hr>
                <div class="row">
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
                </div>
                <div class="col-md-12 jumbotron border">
                    <div class="icon-list">
                        <a href="{{ route('add_provider') }}" title="Aggiungi un fornitore"><i class="verde shadow fa fa-plus-square-o fa-4x"></i></a>
                    </div>
                    @forelse($company as $found)
                            <hr>
                            <div class="row">
                                <h3 class="verde font-weight-bold shadow text-uppercase">{{$found->rag_soc_provider}}</h3>
                            </div>
                            <div class="row">
                                <h5 class="grigio">Dal:&nbsp{{$found->created_at->format('d/m/Y')}}</h5>
                            </div>
                            @if($found->supply_provider=='1')
                                <div class="row">
                                    <a href="{{ route('manage-supply',$found->provider_supply) }}" title="Aggiungi un fornitore"><i class="verde shadow fa fa-info-circle fa-2x"></i></a>
                                    <h5 class="fucsia font-weight-bold shadow">&nbsp;Siete nella stessa rete Supply Chain</h5>
                                </div>
                            @endif
                            <div class="row">
                                <h4 class="grigio shadow">{{'Partita Iva: '.$found->iva_provider}}</h4><br >
                            </div>
                            <div class="row">
                                <h6 class="grigio capitalize">{{$found->address_provider}}</h6>
                            </div>
                            <div class="row">
                                <h6 class="grigio">
                                    @if ($found->telefono_provider!=null)
                                        {{'Telefono: '.$found->telefono_provider}}
                                        @endif
                                        {{' Email: '.$found->email_provider}}</h6>
                            </div>
                            <div class="row">
                                <a href="{{ route('update-provider', $found->id_provider) }}" title="Modifica le informazioni"><i class="btn btn-primary  fa fa-database fa-1x" aria-hidden="true" ></i></a>
                                <a href="{{ route('mapping-providers', $found->id_provider) }}" title="Mapping dei codici prodotto"><i class="btn btn-primary  fa fa-book fa-1x" aria-hidden="true" ></i></a>
                                <a href="{{ route('config-order', $found->id_provider) }}" title="Configurazione ordini d'acquisto"><i class="btn btn-primary  fa fa-cogs fa-1x" aria-hidden="true" ></i></a>
                                <a href="{{ route('generated-order', $found->id_provider) }}" title="Genera un ordine d'acquisto"><i class="btn btn-primary  fa fa-list-ol fa-1x" aria-hidden="true" ></i></a>
                                <a data-toggle="modal" data-target="#exampleModal{{$found->id_provider}}" title="Elimina il fornitore"><i class="btn btn-primary  fa fa-minus-square fa-1x" aria-hidden="true" ></i></a>
                                <div class="col-md-2 text-left">

                                    <!-- Modal -->
                                    <div class="modal fade hide" id="exampleModal{{$found->id_provider}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina il fornitore</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                                    <p>Eliminando il fornitore tutte le informazioni collegate verranno cancellate e non potranno essere recuperate.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$found->id_provider}}">Sei sicuro?</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Modal delete -->
                                    <div class="modal fade bd-example-modal-sm hide" id="deleteModal{{$found->id_provider}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content form-check">
                                                <form onsubmit="showloader()" method="POST" action="{{ route('delete-provider', $found->id_provider) }}">
                                                    {{ csrf_field() }}
                                                    {{'Premi conferma per proseguire con l\'eliminazione'}}
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                        <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$found->id_provider}}">Conferma</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                    @empty
                        <h4 class="fucsia shadow">Clicca il tasto superiore per aggiungere un fornitore</h4>
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