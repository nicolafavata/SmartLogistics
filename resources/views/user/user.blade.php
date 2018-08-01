@extends('layouts.user')
@section('title','Smartlogis per il cittadino')
@section('content')

    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left">
                    <h6> Ciao&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'Accedi ai servizi smart logistcs di '}}
                        {{Auth::user()->comunenow}}</h6>
                </div>
            </div>
        </div>
    </nav>

    <div class="carousel-inner register-business ean_img ">

            <div class="text-center">
                <div class="jumbotron user">
                    <div class="text-center">
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
                    <form onsubmit="showloader()" method="GET" action="{{ route('findproduct') }}">
                        {{ csrf_field() }}
                        <div class="text-center">
                            <h2 class="grigio shadow font-weight-bold">Digita il codice a barre del prodotto</h2>
                            <input required type="text" name="product" maxlength="18" value="{{old('product',$product)}}"><br />
                            <button type="submit" class="btn btn-primary" id="submit_profile">
                                Ricerca
                            </button>
                        </div>
                    </form>
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