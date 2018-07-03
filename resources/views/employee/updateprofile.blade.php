@extends('layouts.employee')
@section('title','Profilo di '.$dati->name.' '.$dati->cognome)

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' puoi aggiornare i seguenti dati'}}</h2>
@endsection

@section('content_section')
        <div id="body_page" class="container-fluid home_employee">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <form onsubmit="showloader()" method="POST" action="{{ route('changemyprofile') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                            <table class="table table-responsive">
                                <tr>
                                    <td rowspan="6">
                                        <img class="border-border-fucsia doppio img-fluid image-responsive" title="{{$dati->name.' '.$dati->cognome}}" alt="Profilo {{$dati->name.' '.$dati->cognome}}"
                                             @if(($dati->img_employee)!=NULL)
                                             src="{{'../storage/'.$dati->img_employee}}">
                                        @else
                                            src="{{'../img/profile.jpg'}}">
                                        @endif
                                    </td>
                                    <td class="grigio shadow destra">
                                        Matricola:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        @if($employee->matricola!=null)
                                            {{$employee->matricola}}
                                        @else
                                            {{'Nessuna'}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Nome:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        {{$dati->name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Cognome:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        {{$dati->cognome}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Email:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        <input type="email" name="email" id="email" class="font-weight-bold lowercase form-group" value="{{old('email', $employee->email)}}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Telefono:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        <input type="text" name="tel_employee" placeholder="0688521478" id="tel_employee" class="font-weight-bold text-uppercase form-group" value="{{old('tel_employee', $employee->tel_employee)}}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Mobile:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        <input type="text" name="cell_employee" placeholder="3397715844" id="cell_employee" class="font-weight-bold text-uppercase form-group" value="{{old('cell_employee', $employee->cell_employee)}}">
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <div class="row center">
                                <button type="submit" class="form-group text-center btn btn-primary pulsante">
                                    Conferma
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