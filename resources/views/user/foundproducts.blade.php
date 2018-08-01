@extends('layouts.user')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner register-business ean_img">
        <br />
        <div class="text-center">


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
                <div class="jumbotron item">
                    <div class="row">
                        <div class="col-md-8 text-left">
                            <h3 class="font-weight-bold text-dark shadow">Ricerca prodotto EAN: {{$product}}</h3>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Negozio</th>
                                <th scope="col"></th>
                                <th scope="col">Descrizione</th>
                                <th scope="col">U.M</th>
                                <th scope="col">Quantità</th>
                                <th scope="col">Prezzo</th>
                                <th scope="col">Dettagli</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($item as $found)
                                <tr>
                                    <td class="font-weight-bold text-center text-dark text-center"><a  data-toggle="modal" data-target="#store{{$found->id_sales_list}}" title="Negozio: {{$found->rag_soc_company}}"><i class="fa fa-shopping-cart fa-2x"></i></a></td>
                                        <td>
                                            <img class="border-inventory"
                                                 @if ($found->url=='0')
                                                 src="img/nofoto.jpg"
                                                 @else
                                                 src="{{$found->url}}"
                                                 @endif
                                                 width="70" height="70" alt="{{$found->title}}"/>
                                        </td>
                                        <td class="font-weight-bold text-dark text-capitalize text-left">
                                            {{$found->title}}
                                        </td>
                                        <td class="font-weight-bold text-dark text-uppercase">
                                            {{$found->unit}}
                                        </td>
                                        <td class="font-weight-bold text-dark">
                                            @if($found->quantity_sales_list=="1")
                                                {{$found->stock}}
                                            @else
                                                {{'N.d.'}}
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-dark text-center">
                                            <?php
                                                $iva = ($found->price_user * $found->imposta)/100;
                                                $tot = $found->price_user + $iva;
                                                $tot = number_format($tot,2);
                                            ?>
                                            {{$tot." €"}}
                                        </td>
                                    <td class="font-weight-bold text-center text-dark text-center"><a  data-toggle="modal" data-target="#product{{$found->id_sales_list}}" title="Negozio: {{$found->rag_soc_company}}"><i class="fucsia fa fa-desktop fa-2x"></i></a></td>
                                </tr>
                            @empty
                                <h6 class="fucsia font-weight-bold shadow">Non abbiamo trovato prodotti per la tua richista</h6>
                            @endforelse
                                    <div class="row">
                                        <div class="col-md-8 push-2">
                                            {{$item->links('vendor.pagination.bootstrap-4')}}
                                        </div>
                                    </div>

                            </tbody>
                        </table>
                        @foreach($item as $found)
                            <!-- Modal Negozio-->
                                <div class="modal fade" id="store{{$found->id_sales_list}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title text-uppercase">{{$found->rag_soc_company}}</h3>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                    <div class="form-check">
                                                        <label class="grigio">{{$found->indirizzo_company.', '.$found->civico_company}}</label><br>
                                                        <label class="grigio">{{'Telefono: '.$found->telefono_company}}</label><br>
                                                        <label class="grigio">{{'Email: '.$found->email_company}}</label><br>
                                                    </div>
                                                    <br />
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    @foreach($item as $found)
                        <!-- Modal Product-->
                            <div class="modal fade" id="product{{$found->id_sales_list}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-uppercase text-shadow">{{$found->title}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 text-left">
                                                    <img class="border-inventory"
                                                         @if ($found->url=='0')
                                                         src="img/nofoto.jpg"
                                                         @else
                                                         src="{{$found->url}}"
                                                         @endif
                                                         width="200" height="200" alt="{{$found->title}}"/>
                                                </div>
                                                <div class="col-md-6 text-left">
                                                    <label class="grigio">{{'Codice del negozio: '.$found->cod_item}}</label><br>
                                                    <label class="grigio">{{'U.m. : '.$found->unit}}</label><br />
                                                        <label class="grigio">{{'Disponibilità: '}}
                                                        @if($found->quantity_sales_list=="1")
                                                            {{$found->stock}}
                                                        @else
                                                            {{'N.d.'}}
                                                        @endif</label><br>
                                                    <label class="grigio">{{'Brand: '.$found->brand}}</label><br>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php
                                                    $iva = ($found->price_user * $found->imposta)/100;
                                                    $tot = $found->price_user + $iva;
                                                    $iva = number_format($iva, 2);
                                                    $tot = number_format($tot, 2);
                                                    $price = number_format($found->price_user,2);
                                                    ?>
                                                    <table class="table table-striped">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col" class="text-left">
                                                                    Prezzo unitario
                                                                </th>
                                                                <th scope="col" class="text-left">
                                                                    Iva
                                                                </th>
                                                                <th scope="col" class="text-left">
                                                                    Totale
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tr>
                                                            <td class="text-left">
                                                                {{$price.' €'}}
                                                            </td>
                                                            <td class="text-left">
                                                                {{$iva.' €'}}
                                                            </td>
                                                            <td class="text-left">
                                                                {{$tot.' €'}}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="col-md-12">
                                                    <table class="table table-striped">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col" class="text-left">
                                                                Descrizione
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tr>
                                                            <td class="text-left">
                                                                {{$found->desc}}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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