@extends('layouts.template')
@section('title','Accedi a Smartlogis')
@section('content')
        <nav class="navbar navbar-light justify-content-between" style="background-color: #ffffff";>
            <div class="container-fluid">
                <div class="col-xs-4">
                    <div class="text-left">
                        <img src="img/logo.gif" width="300" height="55" alt="logo_smartlogis">
                    </div>
                </div>
                <div class="col-xs-5"></div>
                <div class="col-xs-3 text-left">
                    <a href="{{ route('welcome') }}"><button type="button" class="btn">Home</button></a>
                    <a href="{{ route('register') }}"><button type="button" class="btn">Registrati</button></a>
                </div>
            </div>
        </nav>
        <hr>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-2">
                    <div class="panel panel-default">

                        @if( session('status'))
                            @component('components.alert-success')
                                {{session('status')}}
                            @endcomponent
                        @endif
                        @if(session('warning'))
                            @component('components.alert-info')
                                {{session('warning')}}
                            @endcomponent
                        @endif
                        <div class="panel-body">
                            <h1 class="display-5 registrazione">Accedi a Smartlogis</h1>
                            <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">Indirizzo E-mail</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Resta connesso
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn verde">
                                            Accedi
                                        </button>

                                        <a class="btn verde" href="{{ route('password.request') }}" onclick="showloader()">
                                            Hai dimenticato la password?
                                        </a>
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
@section('script')
    @parent

@endsection
