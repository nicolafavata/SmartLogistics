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
                                    <h1 class="shadow grigio center">Foto attuale di {{$dati->name.' '.$dati->cognome}}</h1>
                                    <div class="row form-check center">
                                        <img class="border-border-fucsia doppio img-fluid image-responsive" title="{{$dati->name.' '.$dati->cognome}}" alt="Profilo {{$dati->name.' '.$dati->cognome}}"
                                             @if(($dati->img_employee)!=='0')
                                             src="{{env('APP_URL').'/storage/'.$dati->img_employee}}">
                                        @else
                                            src="{{env('APP_URL').'/img/profile.jpg'}}">
                                        @endif
                                    </div>
                                <br />
                                <form onsubmit="showloader()" method="POST" action="{{ route('changemypicture') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="PATCH">
                                    <div class="form-check center">
                                        <label class="shadow grigio">Seleziona l'immagine dal tuo computer</label>
                                        <input type="file"  name="img_employee" id="img_employee" class="form-control">
                                    </div>

                                    <div class="row destra">
                                        <button type="submit" class="form-group text-center btn btn-primary pulsante" id="submit_picture">
                                            Conferma
                                        </button>
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



@endsection