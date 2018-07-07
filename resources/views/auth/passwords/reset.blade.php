@extends('layouts.reset-password')
@section('title','Smartlogis')
@section('content')
    <nav class="navbar navbar-light justify-content-between" style="background-color: #ffffff";>
        <div class="container-fluid">
            <div class="col-xs-4">
                <div class="text-left">
                    <img src="../../img/logo.gif" width="300" height="55" alt="logo_smartlogis">
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
    <hr>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="h4 panel-heading verde">Imposta una nuova password</div><br>

                <div class="panel-body">
                    <form onsubmit="showloader()" class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Indirizzo email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

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

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Conferma la password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Resetta la password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <hr>
    <hr>
@endsection
@section('footer')
    @parent

@endsection

@section('script')
    @parent

@endsection