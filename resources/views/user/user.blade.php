@extends('layouts.user')
@section('title','Smartlogis per il cittadino')
@section('content')
    <nav class="navbar navbar-light justify-content-between" style="background-color: #91ce0f";>
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> Ciao&nbsp{{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'Accedi ai servizi smart logistcs di '}}
                        {{Auth::user()->comunenow}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <hr>







@endsection
@section('footer')
    @parent

@endsection
@section('script')
    @parent

@endsection