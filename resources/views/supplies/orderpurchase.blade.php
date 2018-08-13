@extends('layouts.document')
@section('title','Ordine fornitore')
@section('content')
    <div class="carousel-inner register-order purchase_img">
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
                        <div class="col-md-12 text-right">
                            <h3 class="font-weight-bold  verde shadow">Ordine d'acquisto</h3>
                        </div>
                    </div>
                    <div id="alert-ajax" roles='alert'>

                    </div>

                        <div class="row">

                            <div class="col-md-12 text-left">
                                <form onsubmit="showloader()" method="post" action="{{ route('update-purchase-order',$id)}}">
                                    {{ csrf_field() }}
                                    <input type="text" name="invoice_salesInvCon" value="{{$id}}" hidden>
                                    <div id="sales_desk" class="p-3 mb-2 bg-white text-dark  font-weight-bold">
                                        <div class="input-group">
                                            <span class="input-group-text">Fornitore:</span>
                                            <span class="form-control">{{$desc_customer}}</span>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text"> Numero:</span>
                                            <span class="font-weight-bold form-control">{{$numberorder}}</span>
                                            <span class="input-group-text"> del:</span> <span class="form-control">{{$date}}</span>
                                            <span class="input-group-text"> Stato dell'ordine</span>
                                            <select  name="state" @if($check->state=='11') class="text-success font-weight-bold" disabled @else class="custom-select" @endif id="state">
                                                    @if($check->state=='10')
                                                        <option class="text-capitalize" value="10"  select >Trasmesso</option>
                                                        <option class="text-capitalize" value="00">Annullato</option>
                                                    @endif
                                                    @if($check->state=='00' or $check->state=='01')
                                                        <option value="00" @if($check->state=='00') select @endif>Annullato</option>
                                                        <option value="01" @if($check->state=='01') select @endif>Non trasmesso</option>
                                                        <option value="10" @if($check->state=='10') select @endif>Trasmesso</option>
                                                    @endif
                                                    @if($check->state=='11') select
                                                        <option value="11"  >Concluso</option>
                                                    @endif
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">Commento:</span>
                                            <input class="form-control" maxlength="190" name="comment" id="comment" type="text" placeholder="Inserisci un commento identificativo" value="{{old('comment',$check->comment)}}">
                                        </div>
                                        <div id="document-content">
                                            <input type="text" name="documentitems[]" hidden id="documentitems" value="{{$json}}">
                                            <table id="content" class="table table-responsive table-sm table-bordered">
                                                <thead>
                                                <tr class="bg-secondary text-white">
                                                    <th scope="col" class="text-center">#</th>
                                                    <th scope="col"></th>
                                                    <th scope="col"></th>
                                                    <th scope="col">Codice</th>
                                                    <th scope="col">Descrizione</th>
                                                    <th scope="col" class="text-center" style="width: 10px">Q.tà</th>
                                                    <th scope="col" class="text-center">U.m.</th>
                                                    <th scope="col" class="text-center">Prezzo</th>
                                                    <th scope="col" class="text-center">Sconti %</th>
                                                    <th scope="col" class="text-center">Totale Imponibile</th>
                                                    <th scope="col"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($content as $item)
                                                    <tr>
                                                        <th scope="row" class="text-center">{{$item->riga}}</th>
                                                            <td class="text-center text-dark text-center">@if($check->state!=='11')<i title="Elimina la riga" class="text-danger fa fa-trash-o"></i>@endif</td>
                                                            <td class="font-weight-bold text-center text-dark text-center">@if($check->state!=='11')<i title="Modifica la riga" class="text-success fa fa-pencil-square-o"></i>@endif</td>
                                                            <td><input disabled class="form-control alert-success" maxlenght="50" type="text" id="code" name="product_salesDeskCon" value="{{$item->codice}}"></td>
                                                            <td><input disabled class="form-control" maxlenght="80" type="text" name="title_product" value="{{$item->title}}"></td>
                                                            <td><input disabled class="form-check-label" size="1" min="1" type="number" step="1.00" name="quantity_salesDeskCon" value="{{$item->quant}}"></td>
                                                        <td><input disabled class="form-control" maxlenght="2" type="text" name="unit" value="{{$item->unit}}"></td>
                                                            <td><input disabled class="form-check-label" type="number" step="0.10" name="price_product" value="{{$item->price}}"></td>
                                                            <?php if($item->perc==0) $discount=null; else $discount=$item->perc ?>
                                                        <td><input disabled class="form-check-label" min="0" type="number" step="0.10" name="discount_salesDeskCon" value="{{$discount}}"></td>
                                                            <?php $tot_price = ($item->price * $item->quant) - ((($item->price * $item->quant)*$item->perc)/100);?>
                                                            <td><input disabled class="form-control" type="text" name="price_product" value="{{$tot_price}}"></td>
                                                            <td class="text-center text-dark text-center"><i  class="verdino fa fa-check"></i></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="input-group-text ">
                                            <span class="input-group-text"> Inserisci prodotto con il codice a barre:</span>
                                            <input @if($check->state=='11') disabled @endif class="form-control alert-success" maxlength="18" id="ean" type="text" placeholder="EAN : 9960085493412">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text text-right"> Totale netto:</span>
                                            <input disabled class="form-control font-weight-bold verde" type="text" value="{{$check->total_no_tax}} €" id="tot" >
                                            <span class="input-group-text text-right"> Iva:</span>
                                            <input disabled class="form-control font-weight-bold verde"  type="text"  value="{{$check->iva}} €" id="iva_tot" >
                                            <span class="input-group-text text-right"> Totale documento:</span>
                                            <input disabled class="form-control font-weight-bold verde" type="text" value="{{($check->total_no_tax+$check->iva)}} €" id="tot_doc" name="tot_doc">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">Riferimento fattura:</span>
                                            <input class="form-control" id="reference" maxlength="40" name="reference" type="text" placeholder="Inserisci un riferimento alla fattura" value="{{old('reference',$check->reference)}}">
                                        </div>
                                        <button @if($check->state=='11') hidden @endif type="button" onclick="addrow('content')" id="add-item" class="btn btn-primary">Aggiungi prodotto</button>
                                        <button id="delete" type="button" class="btn btn-primary" data-toggle="modal" data-target="#cancelladocumento">
                                            Elimina ordine
                                        </button>
                                        @if($check->state=='01')
                                            <button id="transmission" onclick="pushInformation('reference-transmission','comment-transmission','tottransmission','ivatottransmission')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#transmissiondocument">
                                                Trasmissione ordine
                                            </button>
                                        @endif
                                        @if($check->state=='10')
                                            <button onclick="pushInformation('reference-arrive','comment-arrive','totarrive','ivatotarrive')" id="arriveItem" type="button" class="btn btn-primary" data-toggle="modal" data-target="#arrive">
                                                Arrivo merce
                                            </button>
                                        @endif
                                        <input hidden type="text" id="totale" name="totale">
                                        <input hidden type="text" id="ivatotale" name="ivatotale">
                                        <button onclick="pushInformation('reference','comment','totale','ivatotale')" type="submit" class="btn btn-primary" id="submit_document">
                                            Conferma modifiche
                                        </button><br /><br />
                                        download -> <a href="{{route('download-pdf-order',$id)}}"><i title="Pdf" class="text-danger fa fa-file-pdf-o fa-2x"></i></a>
                                        <a href="{{route('download-xml-order',$id)}}"><i title="Xml" class="text-primay fa fa-file-text-o fa-2x"></i></a>
                                    </div>
                                </form>

                                </div>
                            </div>
                        </div>

        </div>
    </div>
    <!-- Modal Delete-->
    <div class="modal fade" id="cancelladocumento" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Eliminazione dell'ordine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ATTENZIONE!!!!

                    @if($check->state=='11') <p>Se elimini un ordine già concluso, la quantità della merce disponibile relativa ad ogni codice movimentato sarà diminuita. Sei sicuro?</p> @endif
                    @if($check->state=='10') <p>Se elimini un ordine già trasmesso, la quantità della merce in arrivo di ogni codice movimentato sarà diminuita. Sei sicuro?</p> @endif
                    @if($check->state=='00' or $check->state=='01') <p>L'eliminazione del documento non comporta modifiche della quantità presente in magazzino. Clicca su Prosegui per eliminare definitivamente l'ordine.</p> @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <form onsubmit="showloader()" method="post" action="{{ route('cancel-purchase-order',$id)}}">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary">Prosegui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Arrive-->
    <div class="modal fade" id="arrive" tabindex="-1" role="dialog" aria-labelledby="arriveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Arrivo della merce</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ATTENZIONE!!!!

                    <p>Confermi la ricezione delle quantità presenti nel documento? Lo stato dell'ordine sarà impostato su "concluso" e non si potranno effettuare più modifiche. Clicca su Prosegui per confermare.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <form onsubmit="showloader()" method="post" action="{{ route('arrive-purchase-order',$id)}}">
                        <input hidden type="text" id="totarrive" name="totarrive">
                        <input hidden type="text" id="ivatotarrive" name="ivatotarrive">
                        <input hidden type="text" name="documentitemsarrive[]"  id="documentitemsarrive" value="{{$json}}">
                        <input hidden name="reference-arrive" type="text" id="reference-arrive">
                        <input hidden name="comment-arrive" type="text" id="comment-arrive">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary">Prosegui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Transmission-->
    <div class="modal fade" id="transmissiondocument" tabindex="-1" role="dialog" aria-labelledby="transmissionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Trasmissione dell'ordine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Conferma oppure inserisci l'indirizzo email del fornitore e clicca su invia.
                    <form onsubmit="showloader()" method="post" action="{{ route('transmission-purchase-order',$id)}}">
                        <input hidden type="text" id="tottransmission" name="tottransmission">
                        <input hidden type="text" id="ivatottransmission" name="ivatottransmission">
                        <input hidden type="text" name="documentitemtransmission[]"  id="documentitemstransmission" value="{{$json}}">
                        <input hidden name="reference-transmission" type="text" id="reference-transmission">
                        <input hidden name="comment-transmission" type="text" id="comment-transmission">
                        {{ csrf_field() }}
                        <input class="form-group" type="email" name="email-provider" value="{{$check->email_provider}}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                            <button type="submit" class="btn btn-primary">Invia</button>
                        </div>
                    </form>
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
            var block = 1;
            var numberRows = {{$number+1}};
            var products = JSON.parse(document.getElementById('documentitems').value);
            var upblock = 1;
            var prova = JSON.stringify(products);
            document.getElementById('documentitems').value = prova;

            $('document').ready(function () {
               $('select').on('click','option.text-capitalize', function (ele) {
                   ele.preventDefault();
                   var state = document.getElementById('state').value;
                   if (state=='10') document.getElementById('arriveItem').hidden = false;
                   if (state=='00') document.getElementById('arriveItem').hidden = true;
               })
            });

            $('document').ready(function () {
                $('div').on('click','i.fa-trash-o', function (ele) {
                    var rows = document.getElementById('content').getElementsByTagName('tr').length;
                    if (rows<=numberRows+1){
                        var e = ele.target;
                        var index = e.parentNode.parentNode.cells[0].innerHTML;
                        var t = e.parentNode.parentNode.cells[9].firstChild;
                        var netto = parseFloat(t.value);
                        var row = products[parseInt(index)-1];
                        var imposta = parseInt(row['imposta']);
                        var iva = parseFloat((netto * imposta)/100);
                        var ivatot = document.getElementById('iva_tot').value;
                        var totnetto = document.getElementById('tot').value;
                        ivatot = parseFloat(ivatot).toFixed(2);
                        totnetto = parseFloat(totnetto).toFixed(2);
                        ivatot = parseFloat(ivatot - iva);
                        totnetto = parseFloat(totnetto -netto);
                        var totdoc = parseFloat(totnetto + ivatot);
                        totdoc = parseFloat(totdoc).toFixed(2);
                        ivatot = parseFloat(ivatot).toFixed(2);
                        totnetto = parseFloat(totnetto).toFixed(2);
                        document.getElementById('iva_tot').value = ivatot + " €";
                        document.getElementById('tot').value = totnetto + " €";
                        document.getElementById('tot_doc').value = totdoc + " €";
                        delete products[parseInt(index)-1];
                        document.getElementById('content').deleteRow(parseInt(index));
                        var rows = document.getElementById('content').getElementsByTagName('tr').length;
                        var indice = parseInt(index);
                        if (rows>=parseInt(indice+1)) {
                            try {
                                for (i=indice; i<rows;i++) {
                                    document.getElementById('content').rows[i].cells[0].innerHTML = i;
                                    var documento2 = products[i];
                                    products[i-1]= {'id_content':documento2['id_content'],'riga':documento2['riga']-1,'codice':documento2['codice'],'quant':documento2['quant'],'discount':documento2['discount'],'product':documento2['product'],'price':documento2['price'],'imposta':documento2['imposta']};
                                }
                                delete products[rows-1];
                            } catch (e) {
                                console.log('eccezione',documento2);
                            }
                        }
                        if (numberRows<=2) {
                            addrow('content');
                            document.getElementById('iva_tot').value = "0.00 €";
                            document.getElementById('tot').value = "0.00 €";
                            document.getElementById('tot_doc').value = "0.00 €";
                        }
                        numberRows = numberRows - 1;
                        block = 1;
                        var prova = JSON.stringify(products);
                        document.getElementById('documentitems').value = prova;
                        document.getElementById('documentitemsarrive').value = prova;
                        document.getElementById('documentitemstransmission').value = prova;
                        if (numberRows==1)  document.getElementById('submit_document').disabled = true;
                        if (numberRows>1)  document.getElementById('submit_document').disabled = false;
                    }
                });
                return;
            });

            $('document').ready(function () {
                $('div').on('click','i.fa-pencil-square-o', function (ele) {
                    if (upblock==1){

                        upblock++;
                        block = 1;
                        var e = ele.target;
                        var index = e.parentNode.parentNode.cells[0].innerHTML;
                        var t = e.parentNode.parentNode.cells[9].firstChild;
                        e.parentNode.parentNode.cells[5].firstChild.disabled = false;
                        e.parentNode.parentNode.cells[8].firstChild.disabled = false;
                        e.parentNode.parentNode.cells[7].firstChild.disabled = false;
                        var data = products[parseInt(index)-1];
                        e.parentNode.parentNode.cells[10].innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><input hidden value="' + data['product'] + '"><i title="Conferma le modifiche" class="text-success fa fa-check-circle-o"></i></td>';
                        var netto = parseFloat(t.value);
                        var row = products[parseInt(index)-1];
                        var imposta = row['imposta'];
                        var iva = parseFloat((netto * imposta)/100);
                        var totconiva = parseFloat(netto + iva);
                        iva = parseFloat(iva);
                        totconiva = parseFloat(totconiva);
                        netto = parseFloat(netto);
                        var ivatot = document.getElementById('iva_tot').value;
                        var totnetto = document.getElementById('tot').value;
                        var totdoc = document.getElementById('tot_doc').value;
                        ivatot = parseFloat(ivatot).toFixed(2);
                        totnetto = parseFloat(totnetto).toFixed(2);
                        totdoc = parseFloat(totdoc).toFixed(2);
                        ivatot = parseFloat(ivatot - iva);
                        totnetto = parseFloat(totnetto - netto);
                        totdoc = parseFloat(totdoc - totconiva);
                        ivatot = parseFloat(ivatot).toFixed(2);
                        totnetto = parseFloat(totnetto).toFixed(2);
                        totdoc = parseFloat(totdoc).toFixed(2);
                        document.getElementById('iva_tot').value = ivatot + " €";
                        document.getElementById('tot').value = totnetto + " €";
                        document.getElementById('tot_doc').value = totdoc + " €";
                        var prova = JSON.stringify(products);
                        document.getElementById('documentitems').value = prova;
                        document.getElementById('documentitemsarrive').value = prova;
                        document.getElementById('documentitemstransmission').value = prova;
                        document.getElementById('ean').disabled = true;
                        document.getElementById('add-item').disabled = true;
                        document.getElementById('submit_document').disabled = true;
                        document.getElementById('arriveItem').disabled = true;
                        document.getElementById('transmission').disabled = true;
                        document.getElementById('delete').disabled = true;
                        e.parentNode.parentNode.cells[2].innerHTML = "<td></td>";
                    }
                });
                return;
            });

            $('document').ready(function (){
                $('div').on('click','i.fa-check-circle-o',function (ele){
                    if (upblock==2){
                        block = 1;
                        upblock=1;
                        var e = ele.target;
                        var index = e.parentNode.parentNode.cells[0].innerHTML;
                        var data = products[parseInt(index)-1];
                        var t = e.parentNode.parentNode.cells[9].firstChild;
                        var netto = parseFloat(t.value);
                        var p = e.parentNode.parentNode.cells[7].firstChild;
                        var imposta = data['imposta'];
                        var price = parseFloat(p.value);
                        var q = e.parentNode.parentNode.cells[5].firstChild;
                        var quant = q.value;
                        var s = e.parentNode.parentNode.cells[8].firstChild;
                        var perc = parseFloat(s.value).toFixed(2);
                        if (perc === 'NaN') perc = 0;
                        if (data['id_content'] === undefined)  data['id_content']='new';
                        products[parseInt(index)-1] = {'id_content':data['id_content'],'riga':data['riga'],'codice':data['codice'],'quant':quant,'discount':perc,'product':data['product'],'price':data['price'],'imposta':data['imposta']};
                        var prova = JSON.stringify(products);
                        document.getElementById('documentitems').value = prova;
                        document.getElementById('documentitemsarrive').value = prova;
                        document.getElementById('documentitemstransmission').value = prova;
                        document.getElementById('ean').disabled = false;
                        document.getElementById('add-item').disabled = false;
                        q.disabled = true;
                        s.disabled = true;
                        p.disabled = true;
                        document.getElementById('submit_document').disabled = false;
                        document.getElementById('arriveItem').disabled = false;
                        document.getElementById('transmission').disabled = false;
                        document.getElementById('delete').disabled = false;
                        var check = e.parentNode.parentNode.cells[10];
                        var del = e.parentNode.parentNode.cells[1];
                        var up = e.parentNode.parentNode.cells[2];
                        del.innerHTML = '<td class="text-center text-dark text-center"><input hidden value="' + numberRows + '"><i title="Elimina la riga" class="text-danger fa fa-trash-o"></i></td>';
                        up.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><input hidden value="' + numberRows + '"><i title="Modifica la riga" class="text-success fa fa-pencil-square-o"></i></td>';
                        check.innerHTML = '<td class="text-center text-dark text-center"><input hidden value="' + data['product'] + '"><i  class="verdino fa fa-check"></i></td>';
                        var ivatot = document.getElementById('iva_tot').value;
                        ivatot = parseFloat(ivatot);
                        var importo = parseFloat(document.getElementById('tot').value);
                        var addiva = parseFloat((netto * imposta)/100);
                        var totiva = parseFloat(ivatot + addiva);
                        var totnetto = parseFloat(netto + importo);
                        var totaledocumento = parseFloat(totnetto + totiva);
                        totiva = parseFloat(totiva).toFixed(2);
                        totnetto = parseFloat(totnetto).toFixed(2);
                        totaledocumento = parseFloat(totaledocumento).toFixed(2);
                        document.getElementById('iva_tot').value = totiva + " €";
                        document.getElementById('tot').value = totnetto + " €";
                        document.getElementById('tot_doc').value = totaledocumento + " €";
                        if (numberRows==1)  document.getElementById('submit_document').disabled = true;
                        if (numberRows>1)  document.getElementById('submit_document').disabled = false;
                    }
                });
                return;
            });

            $('document').ready(function (){
               $('div').on('click','i.fa-check-square-o',function (ele){
                   var rows = document.getElementById('content').getElementsByTagName('tr').length;
                   if (rows>numberRows){
                       console.log('fsafdsa');
                       var e = ele.target;
                       if (e.parentNode.parentNode.cells[3].firstChild === undefined) return; else {
                           var c = e.parentNode.parentNode.cells[3].firstChild;
                           var codice = c.value;
                           var i = e.parentNode.parentNode.cells[7].lastChild;
                           var t = e.parentNode.parentNode.cells[9].firstChild;
                           var imposta = parseFloat(i.value).toFixed(2);
                           var tot = parseFloat(t.value);
                           var q = e.parentNode.parentNode.cells[5].firstChild;
                           var quant = q.value;
                           var p = e.parentNode.parentNode.cells[7].firstChild;
                           var price = parseFloat(p.value).toFixed(2);
                           var s = e.parentNode.parentNode.cells[8].firstChild;
                           var perc = parseFloat(s.value).toFixed(2);
                           if (perc === 'NaN') perc = 0;
                           var i = e.parentNode.parentNode.cells[10].firstChild;
                           var id = i.value;
                           var index = numberRows - 1;
                           products[index] = {'id_content':'new','riga':numberRows,'codice':codice,'quant':quant,'discount':perc,'product':id,'price':price,'imposta':imposta};
                           var prova = JSON.stringify(products);
                           document.getElementById('documentitems').value = prova;
                           document.getElementById('documentitemsarrive').value = prova;
                           document.getElementById('documentitemstransmission').value = prova;
                           numberRows++;
                           block = 1;
                           upblock = 1;
                           document.getElementById('ean').disabled = false;
                           document.getElementById('add-item').disabled = false;
                           q.disabled = true;
                           s.disabled = true;
                           p.disabled = true;
                           var check = e.parentNode.parentNode.cells[10];
                           var del = e.parentNode.parentNode.cells[1];
                           var up = e.parentNode.parentNode.cells[2];
                           del.innerHTML = '<td class="text-center text-dark text-center"><input hidden value="' + numberRows + '"><i title="Elimina la riga" class="text-danger fa fa-trash-o"></i></td>';
                           up.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><input hidden value="' + numberRows + '"><i title="Modifica la riga" class="text-success fa fa-pencil-square-o"></i></td>';
                           check.innerHTML = '<td class="text-center text-dark text-center"><input hidden value="' + id + '"><i  class="verdino fa fa-check"></i></td>';
                           var ivatot = document.getElementById('iva_tot').value;
                           ivatot = parseFloat(ivatot);
                           var importo = document.getElementById('tot').value;
                           importo = parseFloat(importo);
                           var addiva = parseFloat((tot * imposta)/100);
                           var totiva = parseFloat(ivatot + addiva);
                           var totimporto = parseFloat(importo + tot);
                           var totaledocumento = parseFloat(totimporto + totiva);
                           totiva = parseFloat(totiva).toFixed(2);
                           totimporto = parseFloat(totimporto).toFixed(2);
                           totaledocumento = parseFloat(totaledocumento).toFixed(2);
                           document.getElementById('iva_tot').value = totiva + " €";
                           document.getElementById('tot').value = totimporto + " €";
                           document.getElementById('tot_doc').value = totaledocumento + " €";

                           document.getElementById('submit_document').disabled = false;
                           document.getElementById('arriveItem').disabled = false;
                           document.getElementById('transmission').disabled = false;
                           document.getElementById('delete').disabled = false;
                       }
                   }
               });
               return;
            });


            $('document').ready(function () {
                $('div').on('change','input.form-check-label', function (ele) {
                            ele.preventDefault();
                            var element = ele.target;
                            var q = element.parentNode.parentNode.cells[5].firstChild;
                            var tr = element.parentNode.parentNode.cells[7].firstChild;
                            var s = element.parentNode.parentNode.cells[8].firstChild;
                            var perc = parseFloat(s.value).toFixed(2);
                            if (perc === 'NaN') perc = 0;
                            var quant = parseFloat(q.value).toFixed(2);
                            var price = (parseFloat(tr.value).toFixed(2));
                            var totale = parseFloat(quant * price).toFixed(2);
                            var discount = parseFloat((totale * perc) / 100).toFixed(2);
                            var newtotale = parseFloat(totale - discount).toFixed(2);
                            var td = q.parentNode.parentNode.cells[9].firstChild;
                            td.value = newtotale + " €";
                            if (numberRows == 1) document.getElementById('submit_document').disabled = true;
                            if (numberRows > 1) document.getElementById('submit_document').disabled = false;
                });
                return;
            });

            $('document').ready(function () {
                $('div').on('change','input.alert-success', function (ele) {
                    ele.preventDefault();
                    var ean = document.getElementById('ean').value;
                    var e = ele.target;
                    var codice = e.value;
                    if ( codice > ean) var url = '/check-codice-new-order/' + codice; else var url = '/check-ean-new-order/'  + ean;
                    var table = document.getElementById('content');
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
                            success : function (data) {
                                    if ( data['cod'] === undefined) {
                                        document.getElementById('alert-ajax').innerHTML = "<ul class='alert alert-danger alert-dismissible'><li>Il codice: " + codice + ean + " non è presente nel tuo catalogo" +
                                            "</li><button type='button' class='close' onclick='NoHtml()' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></ul>";
                                    } else {
                                        if (numberRows == 1) document.getElementById('content').deleteRow(1);
                                        var rows = document.getElementById('content').getElementsByTagName('tr').length;
                                        if ( codice > ean && rows>2) document.getElementById('content').deleteRow(rows-1);
                                        var rows = document.getElementById('content').getElementsByTagName('tr').length;
                                        if (rows<numberRows+1) {
                                            var row = table.insertRow(rows);
                                            var cell1 = row.insertCell(0);
                                            cell1.innerHTML = '<th scope="row" class="text-center">' + (rows) + '</th>';
                                            var cell2 = row.insertCell(1);
                                            cell2.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><a  onclick="deleterowTableContent('+ (rows) +')" title="Cancella la riga"><i class="text-danger fa fa-minus-circle"></i></a></td>';
                                            var cell3 = row.insertCell(2);
                                            cell3.innerHTML = ' ';
                                            var cell4 = row.insertCell(3);
                                            cell4.innerHTML = '<td><input class="form-control" disabled maxlenght="50" type="text" id="code" name="product_salesDeskCon" value="' + data['cod'] + '"></td>'
                                            var cell5 = row.insertCell(4);
                                            cell5.innerHTML = '<td><input disabled class="form-control" maxlenght="80" type="text" name="title_product" value="' + data['title'] + '"></td>';
                                            var cell6 = row.insertCell(5);
                                            if (data['unit']=='NR') var step = "1"; else var step = "1.10"
                                            cell6.innerHTML = '<td><input class="form-check-label" min="1" type="number" step="'+ step +'" name="quantity_salesDeskCon" value="1"></td>';
                                            var cell7 = row.insertCell(6);
                                            cell7.innerHTML =  '<td><input disabled class="form-control" maxlenght="2" type="text" name="unit" value="' + data['unit'] + '"></td>';
                                            var cell8 = row.insertCell(7);
                                            var price = parseFloat(data['price']);
                                            var tot = parseFloat(price).toFixed(2);
                                            cell8.innerHTML = '<td><input class="form-check-label" type="number" step="0.10" name="price_product" value="'+tot+'" /><input hidden value="'+ data['imposta'] +'" /></td>';
                                            var cell9 = row.insertCell(8);
                                            cell9.innerHTML = '<td><input class="form-check-label" type="number" step="0.10" name="discount_salesDeskCon" value="0.00 %"></td>';
                                            var cell10 = row.insertCell(9);
                                            cell10.innerHTML = '<td><input disabled class="form-control" type="text" name="price_product" value="'+tot+'"></td>';
                                            var cell11 = row.insertCell(10);
                                            cell11.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><input hidden value="' + data['id_inventory'] + '"><i title="Conferma" class="text-success fa fa-check-square-o"></i></td>';
                                            document.getElementById('ean').value = "";
                                            document.getElementById('ean').disabled = true;
                                            document.getElementById('add-item').disabled = true;
                                            upblock = 1;
                                            block=1;
                                            document.getElementById('submit_document').disabled = true;
                                            document.getElementById('arriveItem').disabled = true;
                                            document.getElementById('transmission').disabled = true;
                                            document.getElementById('delete').disabled = true;
                                        }
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