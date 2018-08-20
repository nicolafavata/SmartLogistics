@extends('layouts.productions')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner production_img">
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
                            <h4 class="font-weight-bold text-dark shadow">Le composizioni della produzione</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            @if(count($item)>0)
                                <div class="icon-list">
                                    <a type="button" data-toggle="modal" data-target="#exampleModal{{$dati->id_company_office}}" title="Elimina l'intera produzione"><i class="text-danger shadow fa fa-minus-square-o fa-4x"></i></a>
                                </div>
                            @endif
                            <div class="icon-list">
                                <a href="{{route('add-mapping-production')}}" title="Aggiungi file con le composizioni"><i class="text-success shadow fa fa-plus-square-o fa-4x"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Delete-->
                    <div class="modal fade hide" id="exampleModal{{$dati->id_company_office}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina le composizioni</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                    <p>Eliminando tutte le composizioni perderai le relative informazioni che non potranno più essere recuperarate!</p>
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
                                <form onsubmit="showloader()" method="POST" action="{{ route('delete-mapping-production', $dati->id_company_office)}}">
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
                                <th scope="col" class="text-center">Codice</th>
                                <th scope="col">Descrizione</th>
                                <th scope="col" class="text-center">U.M.</th>
                                <th scope="col" class="text-center">Produttività</th>
                                <th scope="col" class="text-center">Composizione</th>
                                <th scope="col" class="text-center">Elimina</th>
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
                                             @if ($found->url_production=='0')
                                             src="img/nofoto.jpg"
                                             @else
                                             src="{{$found->url_production}}"
                                             @endif
                                             width="70" height="70" alt="{{$found->title_production}}"/>
                                    </td>
                                    <td class="font-weight-bold text-dark text-uppercase text-center">
                                        {{$found->cod_production}}
                                    </td>
                                    <td class="font-weight-bold text-dark text-capitalize">
                                        {{$found->title_production}}
                                    </td>

                                    <td class="font-weight-bold text-center text-dark text-uppercase text-center" >{{$found->unit_production}}</td>
                                    <td class="font-weight-bold text-dark text-center">
                                        {{$found->quantity}}
                                    </td>
                                    <td class="font-weight-bold text-center text-dark text-center"><a  data-toggle="modal" data-target="#composer{{$found->id_production}}"  title="Visualizza la composizione di {{$found->title_production}}"><i class="text-success fa fa-info-circle fa-2x"></i></a></td>
                                    <td class="font-weight-bold text-center text-dark text-center"><a  href="{{'https://www.nicolafavata.com/smartlogis/del-mapping-production/'.$found->id_production}}" title="Elimina la produzione di {{$found->title_production}}"><i class="fucsia fa fa-trash-o fa-2x"></i></a></td>
                                </tr>

                            @empty
                                <h6 class="fucsia font-weight-bold shadow">Non hai caricato le composizioni della produzione</h6>
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
                        <div class="modal fade" id="composer{{$found->id_production}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Composizione del prodotto {{$found->title_production}}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive-sm">
                                            <table class="table table-striped table-responsive">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">Codice</th>
                                                    <th scope="col">Articolo</th>
                                                    <th scope="col">U.M.</th>
                                                    <th scope="col">Quantità</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($composer as $comp)
                                                    @if($comp['id_production']==$found->id_production)
                                                        <tr>
                                                            <td class="font-weight-bold text-dark text-uppercase">
                                                                {{$comp['cod_inventory']}}
                                                            </td>
                                                            <td class="font-weight-bold text-dark text-uppercase">
                                                                {{$comp['title_inventory']}}
                                                            </td>
                                                            <td class="font-weight-bold text-dark text-uppercase">
                                                                {{$comp['unit_inventory']}}
                                                            </td>
                                                            <td class="font-weight-bold text-dark text-uppercase">
                                                                {{$comp['quantity_mapping_production']}}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
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
                var tr = ele.target.parentNode.parentNode.parentNode;
                $.ajax(
                    {
                        url: urldel,
                        type: 'POST',
                        data: '_token={{csrf_token()}}',
                        complete : function (resp) {
                            if (resp.statusText == "OK"){
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