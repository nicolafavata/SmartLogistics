@extends('layouts.sub-supplies')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner supplies_img">
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
                <div class="col-md-12 jumbotron-expires border">
                    <div class="row">
                        <div class="col-md-8 text-left">
                            <h4 class="font-weight-bold text-dark shadow">Mapping con {{$providers->rag_soc_provider}}</h4>
                            <h3>codice fornitore: {{$providers->provider_cod}}</h3>
                        </div>
                        <div class="col-md-4 text-right">
                            @if(count($mapping)>0)
                                <div class="icon-list">
                                    <a type="button" data-toggle="modal" data-target="#exampleModal{{$providers->id_provider}}" title="Elimina l'intero mapping"><i class="text-danger shadow fa fa-minus-square-o fa-4x"></i></a>
                                </div>
                            @endif
                                <div class="icon-list">
                                    <a href="{{route('add_mapping',$providers->id_provider)}}" title="Aggiungi file con il mapping"><i class="text-success shadow fa fa-plus-square-o fa-4x"></i></a>
                                </div>
                        </div>
                    </div>
                    <!-- Modal Delete-->
                    <div class="modal fade hide" id="exampleModal{{$providers->id_provider}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina il mapping con {{$providers->rag_soc_provider}}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                    <p>Eliminando il mapping perderai le relative informazioni che non potranno più essere recuperarate!</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$providers->id_provider}}">Sei sicuro?</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Modal delete -->
                    <div class="modal fade bd-example-modal-sm hide" id="deleteModal{{$providers->id_provider}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content form-check">
                                <form onsubmit="showloader()" method="POST" action="{{ route('delete-mapping', $providers->id_provider)}}">
                                    {{ csrf_field() }}
                                    {{'Premi conferma per proseguire con l\'eliminazione'}}
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                        <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$providers->id_provider}}">Conferma</button>
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
                                <th scope="col">Vs.Codice</th>
                                <th scope="col">Descrizione</th>
                                <th scope="col">U.M.</th>
                                <th scope="col">Mapping</th>
                                <th scope="col">Prezzo unitario</th>
                                <th scope="col">Elimina</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $n=0; ?>
                            @forelse($mapping as $found)
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
                                    <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->unit_inventory}}</td>
                                    <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->cod_mapping_inventory_provider}}</td>
                                    <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->price_provider.' €'}}</td>
                                    <td class="font-weight-bold text-center text-dark text-center"><a  href="{{route('del-mapping',$found->id_mapping_inventory_provider)}}" title="Elimina il mapping del prodotto {{$found->cod_inventory}}"><i class="fucsia fa fa-trash-o fa-4x"></i></a></td>

                            </tr>
                            @empty
                                <h6 class="fucsia font-weight-bold shadow">Non hai effettuato il mapping dei prodotti con il fornitore {{$providers->rag_soc_provider}}</h6>
                            @endforelse
                                    <div class="row">
                                        <div class="col-md-8 push-2">
                                            {{$mapping->links('vendor.pagination.bootstrap-4')}}
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

    @section('ajax')
        <script>
            $('document').ready(function () {
                $('table').on('click','i.fa-trash-o', function (ele) {
                    ele.preventDefault();
                    var urldel = ele.target.parentNode;
                    var tr = ele.target.parentNode.parentNode.parentNode;
                    $.ajax(
                        {
                            url: urldel,
                            type: 'POST',
                            data: '_token={{csrf_token()}}',
                            complete : function (resp) {
                                console.log(resp);
                                if (resp.responseText == 1){
                                tr.parentNode.removeChild(tr);
                                } else {
                                    alert('Problemi con il Server, riprovare tra un pò')
                                }
                            }
                        }
                    );
                })
            })
        </script>
    @endsection

@endsection