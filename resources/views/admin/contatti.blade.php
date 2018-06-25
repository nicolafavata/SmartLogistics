@extends('layouts.sub_admin')
@section('title','Contatti aziendali')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Salve,&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'inserisci i contatti della tua attività'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner admin_contatti">
        <div class="container admin_home">
            <div class="row">
                <div class="col-md-12 jumbotron border">
                    <div class="row">
                        @if(session()->has('message'))
                            @component('components.alert-info')
                                {{session()->get('message')}}
                            @endcomponent
                        @endif
                        @if(count($errors))
                            @component('components.show-errors')
                                {{$errors}}
                            @endcomponent
                        @endif
                    </div>
                    <div class="row">
                        <form onsubmit="showloader()" method="POST" action="{{ route('updatecontatti') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                              <div class="form-group">
                                    <label class="display-3 fucsia shadow font-weight-bold" for="">Contatti</label><br>
                                    <label for="">Telefono</label>
                                    <input type="text" name="telefono" id="telefono" class="font-weight-bold text-uppercase form-group" value="{{old('telefono', $contatti->telefono)}}" ><br />
                                    <label for="">Mobile&nbsp&nbsp&nbsp</label>
                                    <input type="text" name="cellulare" id="cellulare" class="font-weight-bold text-uppercase form-group" value="{{old('cellulare', $contatti->cellulare)}}" ><br />
                                    <label for="">Fax&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                    <input type="text" name="fax" id="fax" class="font-weight-bold text-uppercase form-group" value="{{old('fax', $contatti->fax)}}" ><br />
                                    <label for="">Pec&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                    <input type="email" name="pec" id="pec" class="font-weight-bold text-lowercase form-group" value="{{old('pec', $contatti->pec)}}" ><br />
                                    <label for="">Website</label>
                                    <input type="text" name="web" id="web" class="font-weight-bold text-lowercase form-group" value="{{old('web', $contatti->web)}}" ><br />
                                    <a class="form-group text-center btn btn-primary" href="{{ route('admin') }}" onclick="showloader()">
                                        Annulla
                                    </a>
                                    <button type="submit" class="form-group text-center btn btn-primary" id="submit_profile">
                                        Conferma
                                    </button>
                              </div>
                        </form>
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


@endsection