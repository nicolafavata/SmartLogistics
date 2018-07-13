@extends('layouts.supplies')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner inventories_img">
        <br />
        <div class="container admin_home">
            <div class="row">

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
                <div class="col-md-12 jumbotron-inventory border">
                    <div class="row">
                        <div class="col-md-8 text-left">
                            <h4 class="font-weight-bold text-dark shadow capitalize">Inventario prodotti d'acquisto</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            @if(count($item)>0)
                                <div class="icon-list">
                                    <a type="button" data-toggle="modal" data-target="#exampleModal{{$dati->id_company_office}}" title="Elimina l'inventario"><i class="text-danger shadow fa fa-minus-square-o fa-4x"></i></a>
                                </div>
                            @endif
                            <div class="icon-list">
                                <a href="{{route('add_item')}}" title="Aggiungi nuovi prodotti"><i class="text-success shadow fa fa-plus-square-o fa-4x"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Delete-->
                    <div class="modal fade hide" id="exampleModal{{$dati->id_company_office}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina l'inventario</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                    <p>Eliminando l'inventario perderai la previsione sulle vendite, le vendite effettuate e tutte le relative informazioni, le quali non potranno più essere recuperarate!</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$dati->id_company_office}}">Sei sicuro?</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Modal delete -->
                    <div class="modal fade bd-example-modal-sm hide" id="deleteModal{{$dati->id_company_office}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content form-check">
                                <form onsubmit="showloader()" method="POST" action="{{ route('delete-inventories') }}">
                                    {{ csrf_field() }}
                                    {{'Premi conferma per proseguire con l\'eliminazione'}}
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$dati->id_company_office}}">Conferma</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col"></th>
                                <th scope="col">Codice</th>
                                <th scope="col">Descrizione</th>
                                <th scope="col">Brand</th>
                                <th scope="col">U.M.</th>
                                <th scope="col">Disponibili</th>
                                <th scope="col">Ordinati</th>
                                <th scope="col">Iva</th>
                                <th scope="col">Costo Ultimo</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $n=0; ?>
                            @forelse($item as $found)
                                <?php $n++; ?>
                                <tr>
                                    <th scope="row">
                                        {{$n}}
                                    </th>
                                    <td>
                                        <img class="border-inventory"
                                        @if ($found->url_inventory=='0')
                                            src="img/nofoto.jpg"
                                        @else
                                            src="{{$found->url_inventory}}"
                                        @endif
                                        width="70" height="70" alt="{{$found->title_inventory}}"/></div>
                                    </td>
                                    <td class="font-weight-bold text-dark text-uppercase">
                                        {{$found->cod_inventory}}
                                    </td>
                                    <td class="font-weight-bold text-dark text-capitalize">
                                        {{$found->title_inventory}}
                                    </td>
                                    <td class="font-weight-bold text-dark text-capitalize">
                                        {{$found->brand}}
                                    </td>
                                    <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->unit_inventory}}</td>
                                    <td
                                        @if(($found->stock-$found->committed)<0)
                                            class="font-weight-bold text-danger text-center">
                                    @else
                                            class="font-weight-bold text-dark text-center">
                                        @endif
                                            {{$found->stock-$found->committed}}</td>
                                    <td class="font-weight-bold text-center
                                        @if($found->arriving>0)text-success">
                                        @else text-dark">@endif
                                        {{$found->arriving}}</td>
                                    <td class="font-weight-bold text-dark">{{$found->imposta_desc_inventory}}</td>
                                    <td class="font-weight-bold text-center text-dark">{{number_format($found->last_cost_inventory,2).' ¢'}}</td>
                                </tr>
                            @empty
                                <h6 class="fucsia font-weight-bold shadow">Clicca il tasto verde a destra per aggiungere nuovi prodotti</h6>
                            @endforelse
                                    <div class="row">
                                        <div class="col-md-8 push-2">
                                            {{$item->links('vendor.pagination.bootstrap-4')}}
                                        </div>
                                    </div>

                            </tbody>
                        </table>
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