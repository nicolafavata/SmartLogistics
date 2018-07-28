@extends('layouts.sales')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner sales_img">
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
                            <h4 class="font-weight-bold text-dark shadow">Catalogo vendite</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            @if(count($item)>0)
                                <div class="icon-list">
                                    <a type="button" data-toggle="modal" data-target="#exampleModal{{$dati->id_company_office}}" title="Elimina l'intero listino di vendita"><i class="text-danger shadow fa fa-minus-square-o fa-4x"></i></a>
                                </div>
                            @endif
                                <div class="icon-list">
                                    <a href="{{route('add_catalogue')}}" title="Aggiungi file con il listino prezzi di vendita"><i class="text-success shadow fa fa-plus-square-o fa-4x"></i></a>
                                </div>
                        </div>
                    </div>
                    <!-- Modal Delete-->
                    <div class="modal fade hide" id="exampleModal{{$dati->id_company_office}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina il listino vendite</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                    <p>Eliminando il listino perderai tutti i prezzi dei prodotti!</p>
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
                                <form onsubmit="showloader()" method="POST" action="{{ route('delete-catalogue')}}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="PATCH">
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
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col">Codice</th>
                                <th scope="col">Descrizione</th>
                                <th scope="col">U.M.</th>
                                <th scope="col">Iva</th>
                                <th scope="col">Prezzo</th>
                                <th scope="col">Azzera</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($item as $found)
                                <tr>
                                    <td class="font-weight-bold text-center text-dark text-center"><a  data-toggle="modal" data-target="#setting{{$found->id_sales_list}}" title="Impostazioni del prodotto"><i class="verde fa fa-cog fa-2x"></i></a></td>
                                    @if($found->cod_inventory==null)
                                        <td>
                                            <img class="border-inventory"
                                                 @if ($found->url_production=='0')
                                                 src="img/nofoto.jpg"
                                                 @else
                                                 src="{{$found->url_production}}"
                                                 @endif
                                                 width="70" height="70" alt="{{$found->title_production}}"/>
                                        </td>
                                        <td class="font-weight-bold text-dark text-uppercase">
                                            {{$found->cod_production}}
                                        </td>
                                        <td class="font-weight-bold text-dark text-capitalize">
                                            {{$found->title_production}}
                                        </td>
                                        <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->unit_production}}</td>
                                        <td class="font-weight-bold text-dark">{{$found->imposta_desc_production}}</td>
                                    @else
                                        <td>
                                            <img class="border-inventory"
                                                 @if ($found->url_inventory=='0')
                                                 src="img/nofoto.jpg"
                                                 @else
                                                 src="{{$found->url_inventory}}"
                                                 @endif
                                                 width="70" height="70" alt="{{$found->title_production}}"/>
                                        </td>
                                        <td class="font-weight-bold text-dark text-uppercase">
                                            {{$found->cod_inventory}}
                                        </td>
                                        <td class="font-weight-bold text-dark text-capitalize">
                                            {{$found->title_inventory}}
                                        </td>
                                        <td class="font-weight-bold text-center text-dark text-uppercase" >{{$found->unit_inventory}}</td>
                                        <td class="font-weight-bold text-dark">{{$found->imposta_desc_inventory}}</td>
                                    @endif
                                        <td class="font-weight-bold text-dark text-center">{{$found->price_user." €"}}</td>
                                        <td class="font-weight-bold text-center text-dark text-center"><a  href="{{route('del-catalogue',$found->id_sales_list)}}" title="Azzera i prezzi del prodotto"><i class="fucsia fa fa-trash-o fa-3x"></i></a></td>
                                </tr>
                            @empty
                                <h6 class="fucsia font-weight-bold shadow">Non hai caricato prodotti</h6>
                            @endforelse
                                    <div class="row">
                                        <div class="col-md-8 push-2">
                                            {{$item->links('vendor.pagination.bootstrap-4')}}
                                        </div>
                                    </div>

                            </tbody>
                        </table>
                        @foreach($item as $found)
                            <!-- Modal -->
                                <div class="modal fade" id="setting{{$found->id_sales_list}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                @if($found->cod_inventory==null)
                                                    <h4 class="modal-title">{{$found->cod_production.' - '.$found->title_production}}</h4>
                                                @else
                                                    <h4 class="modal-title">{{$found->cod_inventory.' - '.$found->title_inventory}}</h4>
                                                @endif
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form onsubmit="showloader()" method="POST" action="{{ route('setting-sales',$found->id_sales_list) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="PATCH">
                                                    <div class="form-check">
                                                        <label class="grigio">Prezzo utente finale:&nbsp</label>
                                                        <input required placeholder="Prezzo utente" type="number" step="0.01" name="price_user" id="price_user" class="font-weight-bold" value="{{old('price_user',$found->price_user)}}" >
                                                    </div><br />
                                                    <div class="form-check">
                                                        <label class="grigio">Prezzo b2b:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                                        <input required placeholder="Prezzo b2b" type="number" step="0.01" name="price_b2b" id="price_b2b" class="font-weight-bold" value="{{old('price_b2b',$found->price_b2b)}}" >
                                                    </div><br />
                                                    <div class="form-check">
                                                        <label class="fucsia">Seleziona la visibilità</label>
                                                        <div>
                                                            <input type="checkbox" @if($found->visible_sales_list=="1") checked @endif  name="visible_sales_list" value="1"><label class="grigio">Prezzo di vendita</label>
                                                        </div>
                                                        <div>
                                                            <input type="checkbox" @if($found->quantity_sales_list=="1") checked @endif name="quantity_sales_list" value="1"><label class="grigio">Quantità disponibile</label>
                                                        </div>
                                                    </div><br />
                                                    <br />
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                                        <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                                            Aggiorna
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
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
                  //  alert(urldel);
                    var tr = ele.target.parentNode.parentNode.parentNode.cells[6];

                //    alert(tr);
                    $.ajax(
                        {
                            url: urldel,
                            type: 'POST',
                            data: '_token={{csrf_token()}}',
                            complete : function (resp) {
                                console.log(resp);
                                if (resp.responseText == 1){
                                  tr.innerHTML = "0 €";
                                } else {
                                    alert('Il prodotto è già azzerato')
                                }
                            }
                        }
                    );
                })
            })
        </script>
    @endsection

@endsection