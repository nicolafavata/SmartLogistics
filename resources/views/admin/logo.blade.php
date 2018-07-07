@extends('layouts.sub_admin')
@section('title','Il logo aziendale')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Salve,&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'puoi modificare il logo della tua azienda'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner admin_logo">
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
                        <form onsubmit="showloader()" method="POST" action="{{ route('updatelogo') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                              <div class="form-group">
                                    <label class="display-3 fucsia shadow font-weight-bold" for="">Logo aziendale</label><br>
                                      <img class="border rounded" width="240" height="188" title="logo_azienda" alt="logo_azienda"
                                           @if(($logo->logo)!='0')
                                           src="{{'../storage/'.$logo->logo}}">
                                      @else
                                          src="{{'../img/logo_business.jpg'}}">
                                      @endif
                                    <label class="h6 shadow">Seleziona un logo per la tua azienda (320x250 pixel, tipo di file: jpg / gif / png)</label>
                                    <input type="file"  name="logo" id="logo" class="form-control">
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