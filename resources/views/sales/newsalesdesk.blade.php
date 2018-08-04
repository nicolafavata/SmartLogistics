@extends('layouts.document')
@section('title','Vendita al banco')
@section('content')
    <div class="carousel-inner register-business sales_img">
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
                            <h3 class="font-weight-bold text-dark shadow">Vendita al banco</h3>
                        </div>
                    </div>
                    <div id="alert-ajax" roles='alert'>

                    </div>

                        <div class="row">
                            <div class="col-md-12 text-left">
                                <div id="sales_desk" class="p-3 mb-2 bg-white text-dark  font-weight-bold">
                                    <div class="input-group">
                                        <span class="input-group-text">Cliente:</span>
                                        <input class="form-control" maxlength="18" name="ean" type="text" placeholder="Dati identificativi del cliente">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text"> Numero:</span>
                                        <input required  type="number" name="number_sales_desk" id="number_sales_desk" class="font-weight-bold form-control" value="{{old('number_sales_desk',$number)}}" >
                                        <span class="input-group-text"> del:</span> <input required class="form-control" type="date" name="date_sales_desk" id="date_sales_desk"   value="{{old('date_sales_desk',$date)}}" >

                                        <span class="input-group-text"> Listino prezzi:</span>
                                        <select class="custom-select" id="list" name="list">
                                            <option value="price-user" selected>Clienti</option>
                                            <option value="price-b2b">B2b</option>
                                        </select>
                                    </div>
                                    <div id="document-content">
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
                                                <th scope="col" class="text-center">Prezzo ivato</th>
                                                <th scope="col" class="text-center">Sconti %</th>
                                                <th scope="col" class="text-center">Totale</th>
                                                <th scope="col"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="text-center">1</th>
                                                    <td class="font-weight-bold text-center text-dark text-center"><a  href="#" title="Cancella la riga"><i class="text-danger fa fa-trash-o"></i></a></td>
                                                    <td class="font-weight-bold text-center text-dark text-center"><a  href="#" title="Modifica la riga"><i class="text-success fa fa-pencil-square-o"></i></a></td>
                                                    <td><input class="form-control alert-success" maxlenght="50" type="text" id="code" name="product_salesDeskCon"></td>
                                                    <td><input disabled class="form-control" maxlenght="80" type="text" name="title_product"></td>
                                                    <td><input disabled class="form-check-label" type="number" step="1.00" name="quantity_salesDeskCon"></td>
                                                    <td><input disabled class="form-control" maxlenght="2" type="text" name="unit"></td>
                                                    <td><input disabled class="form-control" type="text" name="price_product"></td>
                                                    <td><input disabled class="form-check" type="number" step="0.10" name="discount_salesDeskCon"></td>
                                                    <td><input disabled class="form-control" type="text" name="price_product"></td>
                                                    <td class="font-weight-bold text-center text-dark text-center"><a  href="#" title="Conferma"><i class="text-success fa fa-check-square-o"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="input-group-text ">
                                        <span class="input-group-text"> Inserisci prodotto con il codice a barre:</span>
                                        <input class="form-control alert-success" maxlength="18" name="ean" id="ean" type="text" placeholder="EAN : 9960085493412">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text text-right"> Totale netto:</span>
                                        <input disabled class="form-control font-weight-bold verde" type="text" value="0.00 €" id="tot" name="tot">
                                        <span class="input-group-text text-right"> Iva:</span>
                                        <input disabled class="form-control font-weight-bold verde"  type="text"  value="0.00 €" id="iva_tot" name="iva_tot">
                                        <span class="input-group-text text-right"> Totale documento:</span>
                                        <input disabled class="form-control font-weight-bold verde" type="text" value="0.00 €" id="tot_doc" name="tot_doc">
                                    </div>
                                    <button type="button" disabled onclick="addrow('content')" id="add-item" class="btn btn-primary">Aggiungi prodotto</button>
                                    <button type="button" class="btn btn-primary">Annulla documento</button>
                                    <button type="button" class="btn btn-primary">Conferma documento</button>
                                </div>
                            </div>
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
            var block = 1;
            var numberRows = 1;
            var document  = new Object();
            $('document').ready(function (){
               $('div').on('click','i.fa-check-square-o',function (ele){
                   var rows = document.getElementById('content').getElementsByTagName('tr').length;
                   if (rows>numberRows){
                       var e = ele.target;
                       var i = e.parentNode.parentNode.cells[7].lastChild;
                       var t = e.parentNode.parentNode.cells[9].firstChild;
                       var imposta = parseFloat(i.value).toFixed(2);
                       var tot = parseFloat(t.value).toFixed(2);
                       var imposta = parseFloat(1+(imposta/100)).toFixed(2);
                       var c = e.parentNode.parentNode.cells[3].firstChild;
                       var codice = c.value;
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
                       document[index] = {'riga':numberRows,'codice':codice,'quant':quant,'discount':perc,'product':id,'price':price};
                       numberRows++;
                       block = 1;
                       document.getElementById('ean').disabled = false;
                       document.getElementById('add-item').disabled = false;
                       q.disabled = true;
                       s.disabled = true;
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
                       var netto = parseFloat(tot / imposta);
                       var addiva = parseFloat(tot - netto);
                       var totiva = parseFloat(ivatot + addiva);
                       var totimporto = parseFloat(importo + netto);
                       var totaledocumento = parseFloat(totimporto + totiva);
                       totiva = parseFloat(totiva).toFixed(2);
                       totimporto = parseFloat(totimporto).toFixed(2);
                       totaledocumento = parseFloat(totaledocumento).toFixed(2);
                       document.getElementById('iva_tot').value = totiva + " €";
                       document.getElementById('tot').value = totimporto + " €";
                       document.getElementById('tot_doc').value = totaledocumento + " €";
                   }
               });
            });

            $('document').ready(function () {
                $('div').on('click','input.form-check', function () {
                    if (block !== undefined) block = 1;
                });
            });

            $('document').ready(function () {
               $('div').on('change','input.form-check', function (ele) {
                   if (block !== undefined){
                       if (block==1) {
                           block++;
                           var s = ele.target;
                           var p = s.parentNode.parentNode.cells[7].firstChild;
                           var q = s.parentNode.parentNode.cells[5].firstChild;
                           var quant = parseFloat(q.value).toFixed(2);
                           var price = parseFloat(p.value).toFixed(2);
                           var perc = parseFloat(s.value).toFixed(2);
                           if (perc === 'NaN') perc = 0;
                           var tot = parseFloat(price * quant).toFixed(2);
                           var discount = parseFloat((perc * tot) / 100).toFixed(2);
                           var newtotale = parseFloat(tot - discount).toFixed(2);
                           var change = s.parentNode.parentNode.cells[9].firstChild;
                           change.value = newtotale + " €";
                       }
                   }
               });
            });

            $('document').ready(function () {
                $('div').on('change','input.form-check-label', function (ele) {
                    ele.preventDefault();
                    var q = ele.target;
                    var tr = q.parentNode.parentNode.cells[7].firstChild;
                    var s = q.parentNode.parentNode.cells[8].firstChild;
                    var perc = parseFloat(s.value).toFixed(2);
                    if (perc === 'NaN') perc = 0;
                    var quant = parseFloat(q.value).toFixed(2);
                    var price = (parseFloat(tr.value).toFixed(2));
                    var totale = parseFloat(quant * price).toFixed(2);
                    var discount = parseFloat((totale * perc)/100).toFixed(2);
                    var newtotale = parseFloat(totale - discount).toFixed(2);
                    var td = q.parentNode.parentNode.cells[9].firstChild;
                    td.value = newtotale + " €";
                })
            });

            $('document').ready(function () {
                $('div').on('change','input.alert-success', function (ele) {
                    ele.preventDefault();
                    var ean = document.getElementById('ean').value;
                    var e = ele.target;
                    var codice = e.value;
                    var id = "{{$id}}";
                    if ( codice > ean) var url = '/check-codice-new-sales/' + id + '/' + codice; else var url = '/check-ean-new-sales/' + id + '/' + ean;
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
                                    if ( codice > ean) document.getElementById('content').deleteRow(rows-1);
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
                                        var list = document.getElementById('list').value;
                                        var cell5 = row.insertCell(4);
                                        cell5.innerHTML = '<td><input disabled class="form-control" maxlenght="80" type="text" name="title_product" value="' + data['title'] + '"></td>';
                                        var cell6 = row.insertCell(5);
                                        if (data['unit']=='NR') var step = "1"; else var step = "1.10"
                                        cell6.innerHTML = '<td><input class="form-check-label" type="number" step="'+ step +'" name="quantity_salesDeskCon" value="1"></td>';
                                        var cell7 = row.insertCell(6);
                                        cell7.innerHTML =  '<td><input disabled class="form-control" maxlenght="2" type="text" name="unit" value="' + data['unit'] + '"></td>';
                                        var cell8 = row.insertCell(7);
                                        if (list=='price-user') var price = parseFloat(data['price_user']).toFixed(2); else var price = parseFloat(data['price_b2b']).toFixed(2);
                                        var iva = parseFloat((data['imposta'] * price) / 100).toFixed(2);
                                        var imposta = parseFloat(data['imposta']).toFixed(0);
                                        var tot = price + iva;
                                        tot = parseFloat(tot).toFixed(2);
                                        cell8.innerHTML = '<td><input disabled class="form-control" type="text" name="price_product" value="'+tot+' €" /><input hidden value="'+imposta+'" /></td>';
                                        var cell9 = row.insertCell(8);
                                        cell9.innerHTML = '<td><input class="form-check" type="number" step="0.10" name="discount_salesDeskCon" value="0.00 %"></td>';
                                        var cell10 = row.insertCell(9);
                                        cell10.innerHTML = '<td><input disabled class="form-control" type="text" name="price_product" value="'+tot+' €"></td>';
                                        var cell11 = row.insertCell(10);
                                        cell11.innerHTML = '<td class="font-weight-bold text-center text-dark text-center"><input hidden value="' + data['id_sales_list'] + '"><i  class="text-success fa fa-check-square-o"></i></td>';
                                        document.getElementById('ean').value = "";
                                        document.getElementById('ean').disabled = true;
                                        document.getElementById('add-item').disabled = true;
                                    }
                                }
                            }
                        }
                    );
                });
            });

            $('document').ready(function () {
                $('div').on('change','input.font-weight-bold', function (ele) {
                    ele.preventDefault();
                    var number = document.getElementById('number_sales_desk').value;
                    var data = document.getElementById('date_sales_desk').value;
                    var id = "{{$id}}";
                    var url = '/check-number-new-sales-desk/' + id + '/' + number + '/' + data;
                    $.ajax(
                        {
                            url: url,
                            type: 'post',
                            data: '_token={{csrf_token()}}',
                            complete : function (resp) {
                                if (resp.responseText == 1){

                                } else {
                                    document.getElementById('number_sales_desk').value = "{{$number}}";
                                    document.getElementById('date_sales_desk').value = "{{$date}}";
                                    var giorno = data.split("-");
                                    var el='alert-ajax';
                                    document.getElementById('alert-ajax').innerHTML = "<ul class='alert alert-danger alert-dismissible'><li>Non puoi inserire questo numero con la data del " + giorno[2] + "/" + giorno[1] + "/" + giorno[0] +
                                        "</li><button type='button' class='close' onclick='NoHtml()' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></ul>";
                                }

                            }
                        }
                    )
                })
            });


        </script>
    @endsection

@endsection