@extends('layouts.sales')
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
                                        <input class="form-control" maxlength="18" name="ean" type="text" placeholder="Riferimento al cliente">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text"> Numero:</span>
                                        <input required  type="number" name="number_sales_desk" id="number_sales_desk" class="font-weight-bold form-control" value="{{old('number_sales_desk',$number)}}" >
                                        <span class="input-group-text"> del:</span> <input required class="form-control" type="date" name="date_sales_desk" id="date_sales_desk"   value="{{old('date_sales_desk',$date)}}" >

                                        <span class="input-group-text"> Listino prezzi:</span>
                                        <select class="custom-select" name="list">
                                            <option value="price-user" selected>Clienti</option>
                                            <option value="price-b2b">B2b</option>
                                        </select>
                                    </div>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                        <tr class="bg-secondary text-white">
                                            <th scope="col" class="text-center">#</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col">Codice</th>
                                            <th scope="col">Descrizione</th>
                                            <th scope="col" class="text-center">Q.tà</th>
                                            <th scope="col" class="text-center">U.m.</th>
                                            <th scope="col" class="text-center">Prezzo ivato</th>
                                            <th scope="col" class="text-center">Sconti %</th>
                                            <th scope="col" class="text-center">Totale</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <div id="content">
                                            <tr>
                                                <th scope="row" class="text-center">1</th>
                                                <td class="font-weight-bold text-center text-dark text-center"><a  href="#" title="Cancella la riga"><i class="text-danger fa fa-trash-o"></i></a></td>
                                                <td class="font-weight-bold text-center text-dark text-center"><a  href="#" title="Modifica la riga"><i class="text-success fa fa-pencil-square-o"></i></a></td>
                                                <td><input class="form-control" maxlenght="50" type="text" name="product_salesDeskCon"></td>
                                                <td><input class="form-control" maxlenght="80" type="text" name="title_product"></td>
                                                <td><input class="form-control" type="number" step="1.00" name="quantity_salesDeskCon"></td>
                                                <td><input disabled class="form-control" maxlenght="2" type="text" name="unit"></td>
                                                <td><input disabled class="form-control" type="text" name="price_product"></td>
                                                <td><input class="form-control" type="number" step="0.10" name="discount_salesDeskCon"></td>
                                                <td><input disabled class="form-control" type="text" name="price_product"></td>
                                                <td class="font-weight-bold text-center text-dark text-center"><a  href="#" title="Conferma"><i class="text-success fa fa-check-square-o"></i></a></td>
                                            </tr>
                                        </div>
                                        </tbody>
                                    </table>
                                    <div class="input-group-text ">
                                        <span class="input-group-text"> Inserisci prodotto per codice a barre:</span>
                                        <input class="form-control alert-success" maxlength="18" name="ean" id="ean" type="text" placeholder="EAN : 9960085493412">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text text-right"> Totale netto:</span>
                                        <input disabled class="form-control font-weight-bold verde" type="text"  name="tot">
                                        <span class="input-group-text text-right"> Iva:</span>
                                        <input disabled class="form-control font-weight-bold verde"  type="text"  name="iva_tot">
                                        <span class="input-group-text text-right"> Totale documento:</span>
                                        <input disabled class="form-control font-weight-bold verde" type="text"  name="tot_doc">
                                    </div>
                                    <button type="button" class="btn btn-primary">Aggiungi prodotto</button>
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
            $('document').ready(function () {
                $('div').on('change','input.alert-success', function (ele) {
                    ele.preventDefault();
                    var ean = document.getElementById('ean').value;
                    var id = "{{$id}}";
                    var url = '/check-ean-new-sales/' + id + '/' + ean;
                    $.ajax(
                        {
                            url: url,
                            type: 'get',
                            data: '_token={{csrf_token()}}',
                            beforeSend: function (xhr) {
                                if (xhr && xhr.overrideMimeType) {
                                    xhr.overrideMimeType('application/json;charset=utf-8');
                                }
                            },
                            dataType: "json",
                            success : function (data) {
                                console.log(data['price_user']);
                                console.log(data['price_b2b']);
                                console.log(data['id_sales_list']);
                                console.log(data['title']);
                                console.log(data['unit']);
                                console.log(data['imposta']);
                                console.log(data['cod']);



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
                                    console.log(document.getElementById('alert'));
                                    document.getElementById('number_sales_desk').value = "{{$number}}";
                                    document.getElementById('date_sales_desk').value = "{{$date}}";
                                    var giorno = data.split("-");
                                    var el='alert-ajax';
                                    document.getElementById('alert-ajax').innerHTML = "<ul class='alert alert-danger alert-dismissible'><li>Non puoi inserire questo numero con la data del " + giorno[2] + "/" + giorno[1] + "/" + giorno[0] +
                                        "</li><button type='button' class='close' onclick='NoHtml()' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></ul>";
                                }

                            }
                        }
                    );
                });
            });


        </script>
    @endsection

@endsection