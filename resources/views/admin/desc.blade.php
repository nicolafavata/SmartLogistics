@extends('layouts.sub_admin')
@section('title','Descrizione attività')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Salve,&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'puoi fornire una breve descrizione della tua attività'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner admin_desc">
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
                        <form onsubmit="showloader()" method="POST" action="{{ route('updatedesc') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                              <div class="form-group">
                                    <label class="display-3 fucsia shadow font-weight-bold" for="">Descrizione attività</label><br>
                                    <textarea name="descrizione" id="descrizione" class="form-control" placeholder="Descrizione attività aziendale">{{old('descrizione',$desc->descrizione)}}</textarea>
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