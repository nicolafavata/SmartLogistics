@extends('layouts.document')
@section('title','Ordini fornitori')
@section('content')
    <div class="carousel-inner purchase-order">
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
                        <h3 class="font-weight-bold text-dark shadow">Ordini d'acquisto</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-left">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="year">Anno</label>
                            </div>
                            <select class="custom-select" id="year">
                                <?php $i=0; ?>
                                @foreach($year as $y)
                                        <option value="{{$year[$i]}}" @if($i==0) selected @endif >{{$year[$i]}}</option>
                                        <?php $i++; ?>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8 text-left">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Fornitore</label>
                            </div>
                            <select class="custom-select" id="provider">
                                <option value="0" selected>Tutti</option>
                                @foreach($provider as $p)
                                    <option value="{{$p->provider_purchase_order}}">{{$p->rag_soc_provider}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="alert-ajax"></div>
                <div id="content">
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Stato</th>
                                <th scope="col"></th>
                                <th scope="col">Nr</th>
                                <th scope="col">Data</th>
                                <th scope="col">Fornitore</th>
                                <th scope="col">Imponibile</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                                <tbody>
                                @forelse($item as $found)
                                    <tr>
                                        <th scope="row">
                                                @if($found->state=='00')
                                                    <i title="Annullato" class="text-danger fa fa-trash-o fa-1x"></i>
                                                @endif
                                                @if($found->state=='01')
                                                    <i title="L'ordine non è stato trasmesso" class="fucsia fa fa-pause fa-1x"></i>
                                                @endif
                                                @if($found->state=='10')
                                                    <i title="Merce in arrivo" class="text-warning fa fa-play fa-1x"></i>
                                                @endif
                                                    @if($found->state=='11')
                                                        <i title="Concluso" class="text-success fa fa-check-circle fa-1x"></i>
                                                    @endif
                                        </th>
                                        <th scope="row">
                                            <i title="{{$found->comment}}" class="text-success fa fa-info fa-1x"></i>
                                        </th>
                                        <td>
                                            {{$found->number}}
                                        </td>
                                        <td>
                                            <?php
                                            $data_str = substr($found->date,0,10);
                                            $data = explode('-',$data_str);
                                            ?>
                                            {{$data[2].'/'.$data[1].'/'.$data[0]}}
                                        </td>
                                        <td>
                                            <small>{{$found->rag_soc_provider}}</small>
                                        </td>
                                        <td class="font-weight-bold text-dark text-capitalize">
                                            <?php $imp = number_format($found->total_no_tax,2, ',', '')?>
                                            {{$imp.' €'}}
                                        </td>
                                        <td>
                                            <a href="{{route('download-pdf-order',$found->id)}}"><i title="Pdf" class="text-danger fa fa-file-pdf-o fa-2x"></i></a>
                                        </td>
                                        <td><a title="Visualizza i dettagli dell'ordine N: {{$found->number}}" href="{{route('view-purchase-order',$found->id)}}"><i class="text-success fa fa-list-ol fa-1x"></i></a></td>
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
        var block=1;

        $('document').ready(function () {
           $('div').on('change','select.custom-select', function (ele) {
              document.getElementById('alert-ajax').innerHTML = "";
              ele.preventDefault();
              var provider = document.getElementById('provider').value;
              var year = document.getElementById('year').value;
              var url = "/purchase-orders/" + year + "/" + provider;
              var check = 1;
              $.ajax(
                  {
                      url: url,
                      type: 'post',
                      data: '_token={{csrf_token()}}',
                      beforeSend: function (xhr) {
                          if (xhr && xhr.overrideMimeType) {
                              xhr.overrideMimeType('application/json;charset=utf-8');
                          }
                      },
                      dataType: "json",
                      success: function (data) {
                          if (block == check){
                              block++;
                              if (data.length > 0){
                                  document.getElementById('content').innerHTML = '<div class="table-responsive-sm"><table id="orders" class="table table-striped"><thead class="thead-light"><tr><th scope="col">Stato</th><th scope="col"></th><th scope="col">Nr</th><th scope="col">Data</th><th scope="col">Fornitore</th><th scope="col">Imponibile</th><th scope="col">Iva</th><th scope="col"></th></tr></thead><tbody></tbody></table></div>';
                                  var rows = data.length;
                                  var i=1;
                                  if (block == 2) {
                                      block++;
                                      data.forEach(function (e){
                                          var table = document.getElementById('orders');
                                          var row = table.insertRow(i);
                                          i++;
                                          var cell1 = row.insertCell(0);
                                          if (e['state']=='00') cell1.innerHTML = '<i title="Annullato" class="text-danger fa fa-trash-o fa-1x"></i>';
                                          if (e['state']=='01') cell1.innerHTML = '<i title="L\'ordine non è stato trasmesso" class="fucsia fa fa-pause fa-1x"></i>';
                                          if (e['state']=='10') cell1.innerHTML = '<i title="Merce in arrivo" class="text-warning fa fa-play fa-1x"></i>';
                                          if (e['state']=='11') cell1.innerHTML = '<i title="Concluso" class="text-success fa fa-check-circle fa-1x"></i>';
                                          var cell2 = row.insertCell(1);
                                          cell2.innerHTML = '<i title="' + e['comment'] + '" class="text-success fa fa-info fa-1x"></i>';
                                          var cell3 = row.insertCell(2);
                                          cell3.innerHTML = e['number'];
                                          var date = e['date'].substr(0,10);
                                          date = date.split('-');
                                          var cell4 = row.insertCell(3);
                                          cell4.innerHTML = date[2] + '/' + date[1] + '/' + date[0];
                                          var cell5 = row.insertCell(4);
                                          cell5.innerHTML = '<small>' + e['rag_soc_provider'] + '</small>';
                                          e['total_no_tax'] = parseFloat(e['total_no_tax']).toFixed(2);
                                          var imp = (e['total_no_tax'].toString()).split('.');
                                          var cell6 = row.insertCell(5);
                                          cell6.innerHTML = imp[0] + ',' + imp[1] + ' €';
                                          cell6.firstChild.parentElement.className = 'font-weight-bold text-dark text-capitalize';
                                          var iva = (e['iva_purchase_order'].toString()).split('.');
                                          var cell7 = row.insertCell(6);
                                          cell7.innerHTML = iva[0] + ',' + iva[1] + ' €';
                                          cell7.firstChild.parentElement.className = 'font-weight-bold text-dark text-capitalize';
                                          var cell8 = row.insertCell(7);
                                          cell8.innerHTML = '<a title="Visualizza i dettagli dell\'ordine N: ' + e['id'] + '" href="/view-orders' + e['id'] + '"><i class="text-success fa fa-list-ol fa-1x"></i></a>';
                                      });
                                  }
                              } else {
                                  document.getElementById('alert-ajax').innerHTML = "<ul class='alert alert-danger alert-dismissible'><li>Non hai effettuato ordini per l'anno selezionato"+
                                      "</li><button type='button' class='close' onclick='NoHtml()' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></ul>";
                              }
                          }
                      }
                  }
              );

           });
            return;
        });

        $('document').ready(function () {
            $('div').on('change','select.custom-select', function (){
                if (block>1) block=1;
            });
            return;
        });
    </script>
@endsection

@endsection