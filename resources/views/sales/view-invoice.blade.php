@extends('layouts.document')
@section('title','Fatture di vendita')
@section('content')
    <div class="carousel-inner register-business">
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
            <div class="item">
                <div class="row">
                    <div class="col-md-12 text-left">
                        <h3 class="font-weight-bold text-dark shadow">Fatture di vendita</h3>
                        <div class="icon-list">
                            <a onclick="showId('add-file')" title="Aggiungi fatture tramite file xml Danea EasyFatt"><i class="text-success shadow fa fa-plus-square-o fa-2x"></i></a>
                        </div>
                        <div hidden id="add-file">
                            <form onsubmit="showloader()" method="POST" action="{{ route('invoice-file') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <h6 class="grigio shadow">Carica il file XML generato dal Software Danea EasyFatt con le fatture che vuoi aggiungere</h6>
                                <input type="file"  name="file-invoice" id="production" class="file-invoice">
                                <button type="submit" class="btn btn-primary" id="submit_picture">
                                            INVIA
                                </button>
                            </form>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Nr</th>
                            <th scope="col">Data</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Totale ivato</th>
                            <th scope="col">Dettagli</th>
                            <th scope="col">Elimina</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($item as $found)
                            <tr>
                                <th scope="row">
                                    {{$found->number_sales_invoice}}
                                </th>
                                <td>
                                    <?php
                                        $data_str = substr($found->date_sales_invoice,0,10);
                                        $data = explode('-',$data_str);
                                    ?>
                                    {{$data[2].'/'.$data[1].'/'.$data[0]}}
                                </td>
                                <td class="font-weight-bold text-dark text-uppercase text-center">
                                    {{$found->customer_reference_invoice}}
                                </td>
                                <td class="font-weight-bold text-dark text-capitalize">
                                    <?php $imp = number_format($found->total_sales_invoice,2, ',', '')?>
                                    {{$imp.' €'}}
                                </td>
                                <td class="font-weight-bold text-center text-dark text-center"><a  data-toggle="modal" data-target="#composer{{$found->id_sales_invoice}}"  title="Visualizza i dettagli della fattura N: {{$found->number_sales_invoice}}"><i class="text-success fa fa-list-ol fa-1x"></i></a></td>
                                <td class="font-weight-bold text-center text-dark text-center"><input hidden value="{{$found->id_sales_invoice}}"><i title="Elimina la fattura n: {{$found->number_sales_invoice}}" class="fucsia fa fa-trash-o fa-1x"></i></td>
                            </tr>

                        @empty
                            <h6 class="fucsia font-weight-bold shadow">Non hai fatture in archivio</h6>
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
                        <div class="modal fade bd-example-modal-lg" id="composer{{$found->id_sales_invoice}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="sales_desk" class="p-3 mb-2 bg-white text-dark  font-weight-bold">
                                            <div class="input-group">
                                                <span class="input-group-text"> Fattura numero: {{' '.$found->number_sales_invoice}}</span>
                                                <span class="input-group-text"> del: {{' '.$data[2].'/'.$data[1].'/'.$data[0]}}</span><br />
                                                <span class="input-group-text">Cliente:{{' '.$found->customer_reference_invoice}}</span>
                                            </div>
                                            <div id="document-content">
                                                <table id="content" class="table table-responsive table-bordered">
                                                    <thead>
                                                    <tr class="bg-secondary text-white">
                                                        <th scope="col" class="text-center">#</th>
                                                        <th scope="col">Codice</th>
                                                        <th scope="col">Descrizione</th>
                                                        <th scope="col" class="text-center" style="width: 10px">Q.tà</th>
                                                        <th scope="col" class="text-center">U.m.</th>
                                                        <th scope="col" class="text-center">Prezzo ivato</th>
                                                        <th scope="col" class="text-center">Sconti %</th>
                                                        <th scope="col" class="text-center">Totale</th>
                                                    </tr>
                                                    </thead>
                                                    <?php
                                                        $n=0;
                                                        $tot=0;
                                                        $netto=0;
                                                        $iva=0;
                                                    ?>
                                                    <tbody>
                                                    @foreach($composer as $comp)
                                                        @if($comp->invoice_salesInvCon==$found->id_sales_invoice)
                                                            <tr>
                                                                <?php
                                                                    $n++;
                                                                    $price = $comp->price_salesInvCon;
                                                                    if ($comp->discount_salesInvCon>0) {
                                                                        $price = $price - (($comp->price_salesInvCon * $comp->discount_salesInvCon)/100);
                                                                        $discount = round($comp->discount_salesInvCon,2);
                                                                        $perc = $discount.' %';
                                                                    }
                                                                    else $perc = '';
                                                                    $totale = $price * $comp->quantity_salesInvCon;
                                                                    $tot = $tot + $totale;
                                                                    $netto_unit = $totale / $comp->imposta_salesInvCon;

                                                                    $iva_unit = $totale - $netto_unit;
                                                                    $netto = $netto + $netto_unit;
                                                                    $iva = $iva + $iva_unit;
                                                                    $price = number_format($price,2);
                                                                    $quantity = number_format($comp->quantity_salesInvCon,2);
                                                                    $totale = number_format($totale,2);
                                                                    if ($comp->cod_inventory==null){
                                                                        $code = $comp->cod_production;
                                                                        $title = $comp->title_production;
                                                                        $unit = $comp->unit_production;
                                                                    } else {
                                                                        $code = $comp->cod_inventory;
                                                                        $title = $comp->title_inventory;
                                                                        $unit = $comp->unit_inventory;
                                                                    }
                                                                ?>

                                                                    <th scope="row" class="text-center">{{$n}}</th>
                                                                    <td><span>{{$code}}</span></td>
                                                                    <td><span>{{$title}}</span></td>
                                                                    <td><span>{{$quantity}}</span></td>
                                                                    <td><span>{{$unit}}</span></td>
                                                                    <td><span>{{$price.' €'}}</span></td>
                                                                    <td><span>{{$perc}}</span></td>
                                                                    <td><span>{{$totale.' €'}}</span></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php
                                                $netto = number_format($netto,2);
                                                $iva = number_format($iva,2);
                                                $tot = number_format($tot,2)
                                            ?>
                                            <div class="input-group-text">
                                                <span> Totale netto: </span><span class="verde font-weight-bold">&nbsp;{{$netto.' € '}}&nbsp;&nbsp;</span>
                                                <span> Iva: </span><span class="verde font-weight-bold"> &nbsp;{{$iva.' € '}}&nbsp;&nbsp;</span>
                                                <span> Totale documento: </span><span class="verde font-weight-bold">&nbsp;{{$tot.' €'}}</span>
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

@section('ajax')
    <script>
        $('document').ready(function () {
            $('table').on('click','i.fa-trash-o', function (ele) {
                ele.preventDefault();
                var e = ele.target.parentNode.firstChild;
                var url = "/cancel-invoice-sale/" + e.value;
                var tr = ele.target.parentNode.parentNode;
                $.ajax(
                    {
                        url: url,
                        type: 'post',
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