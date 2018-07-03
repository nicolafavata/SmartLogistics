@extends('layouts.employee')
@section('title','Smartlogis per le aziende')

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' da qui puoi modificare la foto del tuo profilo'}}</h2>
@endsection

@section('content_section')
        <div class="container-fluid home_employee picture_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <div class="row">

                          
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