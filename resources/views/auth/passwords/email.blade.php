@extends('layouts.template')
@section('title','Smartlogis')
@section('content')
    <nav class="navbar navbar-light justify-content-between" style="background-color: #ffffff";>
        <div class="container-fluid">
            <div class="col-xs-4">
                <div class="text-left">
                    <img src="{{env('APP_URL')}}/img/logo.gif" width="300" height="55" alt="logo_smartlogis">
                </div>
            </div>
            <div class="col-xs-5"></div>
            <div class="col-xs-3 text-left">
                <a href="{{ route('welcome') }}"><button type="button" class="btn">Home</button></a>
            </div>
        </div>
    </nav>
    <hr>
    <hr>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1 class="display-5 registrazione">Richiedi una nuova password</h1>

                        <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">Indirizzo email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        @component('components.alert-info')
                                            {{$errors->first('email')}}
                                        @endcomponent
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn verde">
                                        Invia una nuova password
                                    </button>
                                </div>
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
@section('web_script')
    <script type="text/javascript" src="../js/myjs.js"></script>
    @endsection
@section('script')
    @parent

@endsection
