@extends('layouts.document')
@section('title','Ordini clienti')
@section('content')
    <?php
        $date = date('Y-m-d');
    ?>
    <input type="hidden" id="tok" value="{{csrf_token()}}">
    <input type="hidden" id="now" value="{{$date}}">
    <div class="carousel-inner register-order">
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
            <div class="item" id="form">
                <div class="row">
                    <div class="col-md-12 text-left">
                        <h3 class="font-weight-bold text-dark shadow">Gli ordini dei clienti</h3>
                        <div class="icon-list">
                            <a onclick="showId('add-file')" title="Aggiungi Ordini clienti tramite file xml Danea EasyFatt"><i class="text-success shadow fa fa-plus-square-o fa-2x"></i></a>
                        </div>
                        <div id="alert-ajax" roles='alert'>

                        </div>
                        <div hidden id="add-file">
                            <form onsubmit="showloader()" method="POST" action="{{ route('order-sales-file') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <h6 class="grigio shadow">Carica il file XML generato dal Software Danea EasyFatt con gli ordini che vuoi aggiungere</h6>
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
                            <th scope="col">Stato dell'ordine</th>
                            <th scope="col">Nr</th>
                            <th scope="col">Data</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Totale ivato</th>
                            <th scope="col">Dettagli</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($item as $found)
                            <tr>
                                <td class="center">
                                    @if($found->state_salesCust_or=='1')
                                            <a  title="L'ordine N: {{$found->number_sales_invoice}} è stato chiuso"><i class="fucsia fa fa-times-circle fa-1x"></i></a>
                                        @else
                                            <a  data-toggle="modal" data-target="#open-document{{$found->id_sales_invoice}}"  title="Ordini aperti del cliente {{$found->customer_reference_invoice}}"><input value="{{$found->customer_reference_invoice}}" hidden><i class="text-success fa fa-folder-open-o"></i></a>
                                    @endif
                                </td>
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
                                <td class="font-weight-bold text-center text-dark text-center">
                                    @if($found->state_salesCust_or=='1')
                                            <a  data-toggle="modal" data-target="#document{{$found->id_sales_invoice}}"  title="Visualizza il documento emesso per l'ordine N: {{$found->number_sales_invoice}}"><i class="text-success fa fa-info-circle"></i></a>
                                        @else
                                            <input hidden value="{{$found->id_sales_invoice}}"><i title="Elimina l'ordine n: {{$found->number_sales_invoice}}" class="fucsia fa fa-trash-o fa-1x"></i>
                                        @endif
                                </td>
                            </tr>

                        @empty
                            <h6 class="fucsia font-weight-bold shadow">Non hai ordini in archivio</h6>
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
                        <div class="modal fade bd-example-modal-sm" id="document{{$found->id_sales_invoice}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @foreach($document as $doc)
                                            @if($doc->id_salesCust_or==$found->id_sales_invoice)
                                                @if($doc->number_sales_invoice==null)
                                                    <?php
                                                        $giorno = substr($doc->date_sales_desk,8,2);
                                                        $mese = substr($doc->date_sales_desk,5,2);
                                                        $anno = substr($doc->date_sales_desk,0,4);
                                                        ?>
                                                    <div class="input-group">
                                                        <span class="input-group-text"> Vendita al banco numero</span>
                                                        <input class="form-group font-weight-bold" value="{{$doc->number_sales_desk}}" disabled>
                                                        <span class="input-group-text"> del: </span>
                                                        <input class="form-group font-weight-bold" type="text" value="{{$giorno.'/'.$mese.'/'.$anno}}" disabled>
                                                        @if($doc->customer_reference!==null)
                                                            <span class="input-group-text">Cliente:</span>
                                                            <input class="form-group font-weight-bold" value="{{$doc->customer_reference}}" disabled>
                                                        @endif
                                                    </div>
                                                        <span> Totale documento: </span><span class="verde font-weight-bold">&nbsp;{{number_format($doc->total_sales_desk,2, ',', '').' €'}}</span>
                                                    @else
                                                    <?php
                                                        $giorno = substr($doc->date_sales_invoice,8,2);
                                                        $mese = substr($doc->date_sales_invoice,5,2);
                                                        $anno = substr($doc->date_sales_invoice,0,4);
                                                        ?>
                                                        <div class="input-group">
                                                            <span class="input-group-text"> Fattura numero</span>
                                                            <input class="form-group font-weight-bold" value="{{$doc->number_sales_invoice}}" disabled>
                                                            <span class="input-group-text"> del: </span>
                                                            <input class="form-group font-weight-bold" type="text" value="{{$giorno.'/'.$mese.'/'.$anno}}" disabled>
                                                            @if($doc->customer_reference_invoice!==null)
                                                                <span class="input-group-text">Cliente:</span>
                                                                <input class="form-group font-weight-bold" value="{{$doc->customer_reference_invoice}}" disabled>
                                                            @endif
                                                        </div>
                                                        <span> Totale documento: </span><span class="verde font-weight-bold">&nbsp;{{number_format($doc->total_sales_invoice,2, ',', '').' €'}}</span>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach
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
                                                <span class="input-group-text"> Ordine numero: {{' '.$found->number_sales_invoice}}</span>
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
                                                                    $price = number_format($price,2, ',', '');
                                                                    $quantity = number_format($comp->quantity_salesInvCon,2, ',', '');
                                                                    $totale = number_format($totale,2, ',', '');
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
                                                $netto = number_format($netto,2, ',', '');
                                                $iva = number_format($iva,2, ',', '');
                                                $tot = number_format($tot,2, ',', '')
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
           $('table').on('click','i.fa-folder-open-o', function (ele) {
              ele.preventDefault();
              var e = ele.target.parentNode.firstChild;
              var rag = e.value.substr(0,25);
              var rag = rag +'%'
              var url = "https://www.nicolafavata.com/smartlogis/order-sales-open";
              var customer = e.value;
              $.ajax({
                  url: url,
                  type: 'get',
                  data: 'rag='+rag,
                  beforeSend: function (xhr) {
                      if (xhr && xhr.overrideMimeType) {
                          xhr.overrideMimeType('application/json;charset=utf-8');
                      }
                  },
                  dataType: "json",
                  success : function (data){
                      if ( data.length === 0) {
                          document.getElementById('alert-ajax').innerHTML = "<ul class='alert alert-danger alert-dismissible'><li>Il cliente non ha ordini aperti</li><button type='button' class='close' onclick='NoHtml()' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></ul>";
                      } else {
                          var tok = document.getElementById('tok').value;
                          document.getElementById('form').innerHTML='<form onsubmit="showloader()" method="post" action="https://www.nicolafavata.com/smartlogis/close-order-sales-open"><input hidden name="customer" value="'+customer+'">' +
                               '<input type="hidden" name="_token" value="'+tok+'">' +
                              '<div id="orders"></div><a type="button" class="btn btn-primary" href="https://www.nicolafavata.com/smartlogis/view-sales-order">Indietro</a> <button type="submit" class="btn btn-primary" id="submit_document">' +
                              '                                            Conferma' +
                              '                                        </button></form>';
                          document.getElementById('orders').innerHTML = '<div class="row">' +
                              '                    <div class="col-md-12 text-left">' +
                              '                        <h3 class="font-weight-bold text-dark shadow">Gli ordini aperti del cliente:</h3><h4 class="fucsia shadow">'+ customer +'</h4>' +
                              '                    </div>' +
                              '                </div>'+'<div class="input-group mb-3">' +
                              '  <div class="input-group-prepend">' +
                              '    <label class="input-group-text" for="inputGroupSelect01">Tipo di documento</label>' +
                              '  </div>' +
                              ' <select name="type" class="custom-select" id="type-document" onchange="showNumberDate()">' +
                              '<option class="input-group" selected>Seleziona il tipo di documento</option>' +
                              '<option class="input-group" value="invoice">Fattura</option>' +
                              '    <option value="desk">Vendita al banco</option>' +
                              '  </select>' +
                              '</div>'+
                                  '<div id="numdat" hidden class="input-group">' +
                              '                                            <span class="input-group-text">Numero:</span>' +
                              '                                            <input required  type="number" name="number" id="number" class="font-weight-bold form-control" >' +
                              '                                            <span class="input-group-text"> del:</span> <input required class="form-control" type="date" name="date" id="date" >' +
                              '                                        </div>'+
                              '<div class="table-responsive-sm">' +
                              '                    <table id="content" class="table table-striped border">' +
                              '                        <thead class="thead-light">' +
                              '                        <tr>' +
                              '                            <th scope="col"></th>' +
                              '                            <th scope="col">Nr</th>' +
                              '                            <th scope="col">Data</th>' +
                              '                            <th scope="col">Totale ivato</th>' +
                              '                        </tr>' +
                              '                        </thead>' +
                              '                        <tbody> </tbody>' +
                          '                    </table></div>'+
                              '<div class="row">' +
                              '                    <div class="col-md-12 text-left">' +
                              '                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="text-success font-weight-bold  fa fa-long-arrow-up">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seleziona gli ordini che vuoi chiudere</i>' +
                              '                    </div>' +
                              '                </div>';
                          var i=1;
                              data.forEach(function (e) {
                                  var table = document.getElementById('content');
                                  var row = table.insertRow(i);
                                  i++;
                                  var cell1 = row.insertCell(0);
                                  cell1.innerHTML = '<td class="text-center"><div class="input-group mb-3">' +
                                    '  <div class="input-group-prepend">' +
                                    '  <div class="input-group-text">' +
                                    '  <input type="checkbox" name="document[]" value="'+e['id']+'" aria-label="Checkbox for following text input">'+
                                    ' </div></div> </td>';
                                  var cell2 = row.insertCell(1);
                                  cell2.innerHTML = '<th scope="row" class="text-center font-weight-bold">' + e['number'] + '</th>';
                                  var cell3 = row.insertCell(2);
                                  e['date'] = e['date'].substr(0,10);
                                  var data_doc = e['date'].split("-");
                                  cell3.innerHTML = '<td class="text-center font-weight-bold">'+data_doc[2]+'/'+data_doc[1]+'/'+data_doc[0]+'</td>';
                                  var cell4 = row.insertCell(3);
                                  var total = parseFloat(e['total']).toFixed(2);
                                  cell4.innerHTML = '<td class="text-center font-weight-bold">'+total+'&nbsp;€</td>';

                              });


                      }
                  }
              });
           });
           return;
        });

        $('document').ready(function () {
            $('table').on('click','i.fa-trash-o', function (ele) {
                ele.preventDefault();
                var e = ele.target.parentNode.firstChild.nextSibling;
                var url = "https://www.nicolafavata.com/smartlogis/cancel-sales-order/" + e.value;
                var tr = ele.target.parentNode.parentNode;
                $.ajax(
                    {
                        url: url,
                        type: 'post',
                        data: '_token={{csrf_token()}}',
                        complete : function (resp) {
                            if (resp.responseText == 1){
                                tr.parentNode.removeChild(tr);
                            } else {
                                alert('Problemi con il Server, riprovare tra un pò')
                            }
                        }
                    }
                );
            });
            return;
        });
    </script>
@endsection

@endsection