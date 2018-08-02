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
                <div class="jumbotron jumbotron-fluid item">
                    <div class="row">
                        <div class="col-md-12 text-left">
                            <h4 class="font-weight-bold text-dark shadow">Vendita al banco</h4>
                        </div>
                    </div>
                    <div id="alert-ajax" roles='alert'>

                    </div>
                    <div class="jumbotron border border-dark document">

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
                    console.log('fda');
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
                })
            })

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
                })
            })


        </script>
    @endsection

@endsection