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
                            <h5 class="font-weight-bold text-dark shadow text-uppercase">Mapping con {{$providers->rag_soc_provider}}</h5>
                            <small class="fucsia uppercase">codice fornitore: {{$providers->provider_cod}}</small>
                        </div>
                        <div class="col-md-4 text-right">
                            @if(count($mapping)>0)
                                <div class="icon-list">
                                    <a type="button" data-toggle="modal" data-target="#exampleModal{{$providers->id_provider}}" title="Elimina l'intero mapping"><i class="text-danger shadow fa fa-minus-square-o fa-2x"></i></a>
                                </div>
                            @endif
                                <div class="icon-list">
                                    <a href="{{route('add_mapping',$providers->id_provider)}}" title="Aggiungi file con il mapping"><i class="text-success shadow fa fa-plus-square-o fa-2x"></i></a>
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
                                <th scope="col">Ns.Codice</th>
                                <th scope="col">Descrizione</th>
                                <th scope="col">U.M.</th>
                                <th scope="col">Mapping</th>
                                <th scope="col">Prezzo</th>
                                <th scope="col">Modifica</th>
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
                                    <td class="font-weight-bold text-dark text-uppercase text-center">
                                        {{$found->cod_inventory}}
                                    </td>
                                    <td class="font-weight-bold text-dark text-capitalize">
                                        {{$found->title_inventory}}
                                    </td>
                                    <?php $price = number_format($found->price_provider,2, ',', '') ?>
                                    <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->unit_inventory}}</td>
                                    <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->cod_mapping_inventory_provider}}</td>
                                    <td class="font-weight-bold text-center text-dark text-uppercase text-center" >{{$price.' €'}}</td>
                                    <td class="text-center"><a data-toggle="modal" data-target="#changeMapping{{$found->id_mapping_inventory_provider}}"><i title="Modifica il mapping" class="text-success fa fa-pencil-square-o fa-2x"></i></a></td>

                                    <td class="font-weight-bold text-center text-dark text-center"><a  href="{{route('del-mapping',$found->id_mapping_inventory_provider)}}" title="Elimina il mapping del prodotto {{$found->cod_inventory}}"><i class="fucsia fa fa-trash-o fa-2x"></i></a></td>

                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="changeMapping{{$found->id_mapping_inventory_provider}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title font-weight-bold fucsia" id="exampleModalLabel">{{$found->title_inventory}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form onsubmit="showloader()" method="POST" action="{{ route('update-mapping',$found->id_mapping_inventory_provider) }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="PATCH">
                                                <input type="hidden" name="code" value="{{$found->id_inventory}}">
                                                <input type="hidden" name="provider" value="{{$found->id_provider}}">
                                                <div class="form-group">
                                                    <label class="form-check-inline">Nostro codice:</label>
                                                    <span class="font-weight-bold grigio shadow">{{$found->cod_inventory}}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-check-inline">Codice Fornitore:</label>
                                                    <input class="form-check-label text-uppercase" type="text" name="newmapping" required value="{{old('newmapping',$found->cod_mapping_inventory_provider)}}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-check-inline">Prezzo Fornitore:</label>
                                                    <input class="form-check-label"min="1" type="number" step="0.01"  name="newprice"  required value="{{$found->price_provider}}">
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">Categoria</label>
                                                    </div>
                                                    <select class="custom-select"  name="first">
                                                        <option value="1" @if($found->first=="1") selected @endif >Principale</option>
                                                        <option value="0" @if($found->first=="0") selected @endif>Secondaria</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                                    <button type="submit" class="btn btn-primary">Aggiorna</button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @empty
                                <h6 class="fucsia font-weight-bold shadow text-uppercase">Non hai effettuato il mapping dei prodotti con il fornitore {{$providers->rag_soc_provider}}</h6>
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