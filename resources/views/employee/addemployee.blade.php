@extends('layouts.employee')
@section('title','Aggiungi un impiegato')

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' puoi aggiungere un impiegato alla tua sede'}}</h2>
@endsection

@section('content_section')
        <div id="body_page" class="container-fluid home_employee employee_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <form onsubmit="showloader()" method="POST" action="{{ route('addnewemployee') }}">
                                {{ csrf_field() }}
                            <div class="form-check">
                                <label class="grigio">Nome:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input required maxlength="255" placeholder="Nome dell'impiegato" type="text" name="name" id="name" class="font-weight-bold" value="{{old('name')}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Cognome:&nbsp</label>
                                <input required maxlength="255" placeholder="Cognome dell'impiegato" type="text" name="cognome" id="cognome" class="font-weight-bold" value="{{old('cognome')}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="fucsia">Seleziona i privilegi&nbsp</label>
                                <div>
                                    <input type="checkbox" name="acquisti" value="1"><label class="grigio">Acquisti&nbsp&nbsp</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="produzione" value="1"><label class="grigio">Produzione&nbsp&nbsp</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="vendite" value="1"><label class="grigio">Vendite&nbsp&nbsp</label>
                                </div>
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Matricola:&nbsp&nbsp&nbsp&nbsp</label>
                                <input maxlength="16" placeholder="Identificativo" type="text" name="matricola" id="matricola" class="font-weight-bold uppercase form-group"
                                       value="{{old('matricola')}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Telefono:&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input maxlength="16" placeholder="0277841256" type="text" name="tel_employee" id="tel_employee" class="font-weight-bold uppercase form-group"
                                       value="{{old('tel_employee')}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Mobile:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input placeholder="+393485517452" type="text" name="cell_employee" id="cell_employee" class="font-weight-bold uppercase form-group"
                                       maxlength="16" value="{{old('cell_employee')}}" >
                            </div><br />
                            <div class="form-check{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="grigio">E-Mail:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input required maxlength="255" id="email" type="email" class="form-control font-weight-bold lowercase" name="email" value="{{ old('email') }}"  placeholder="example: info@email.it">
                            </div><br />
                            <div class="form-check{{ $errors->has('password') ? ' has-error' : '' }}">


                                <div class="form-group">
                                    <label class="grigio">Password:&nbsp</label>
                                    <input maxlength="255" id="password" type="password" class="form-control" name="password" required>
                                </div><br />
                                <div class="form-check">


                                    <div class="form-group">
                                        <label class="grigio">Conferma:&nbsp</label>
                                        <input maxlength="255" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div><br />
                            <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                Conferma
                            </button>
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