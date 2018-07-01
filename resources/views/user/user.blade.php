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
    <hr>







@endsection
@section('footer')
    @parent

@endsection
@section('script')
    @parent

@endsection