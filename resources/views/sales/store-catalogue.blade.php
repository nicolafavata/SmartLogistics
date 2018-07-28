@extends('layouts.sales')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner production_img">
        <br />
        <div class="container admin_home">
            <div class="row">

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
                <div class="col-md-12 jumbotron-inventory border">
                    <div class="row">
                        <div class="col-md-12 text-left">
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi i listini di vendita e la visibilità</h4>
                        </div>
                        <form onsubmit="showloader()" method="POST" action="{{ route('upload-catalogue') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-12 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="verde font-weight-bold shadow">Carica il file con i listini</h3>
                                        </div>
                                        <input type="file"  name="catalogue" id="catalogue" class="form-control">
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-12">
                                    <button type="submit" class="text-center btn btn-primary" id="submit_picture">
                                        INVIA
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