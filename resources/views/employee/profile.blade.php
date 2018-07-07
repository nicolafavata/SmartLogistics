@extends('layouts.employee')
@section('title','Profilo di '.$dati->name.' '.$dati->cognome)

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' questi sono i dati del tuo profilo'}}</h2>
@endsection

@section('content_section')
        <div id="body_page" class="container-fluid home_employee">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                            <table class="table table-responsive">
                                @if($dati->responsabile=='1')
                                    <tr>
                                        <td class="fucsia ombra" colspan="3">
                                            <h2>Sei il responsabile della sede di </h2><h2 class=" grigio ombra uppercase">{{$dati->rag_soc_company}}</h2>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="fucsia ombra" colspan="3">
                                            <h2>&nbsp;Ti occupi di
                                            @if($dati->acquisti=='1')
                                                &nbsp;acquisti
                                                @endif
                                                @if($dati->produzione=='1')
                                                    &nbsp;produzione
                                                    @endif
                                                @if($dati->vendite=='1')
                                                    &nbsp;vendite
                                                    @endif
                                            </h2>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="grigio ombra" colspan="2">
                                        <h3>Creazione account:&nbsp{{$employee->created_at->format('d/m/Y')}}</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="6">
                                        <img class="border-border-fucsia doppio img-fluid image-responsive" title="{{$dati->name.' '.$dati->cognome}}" alt="Profilo {{$dati->name.' '.$dati->cognome}}"
                                             @if(($dati->img_employee)!='0')
                                             src="{{env('APP_URL').'/storage/'.$dati->img_employee}}">
                                        @else
                                            src="{{env('APP_URL').'/img/profile.jpg'}}">
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
                                        {{$employee->email}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Telefono:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        @if($employee->tel_employee!=null)
                                        {{$employee->tel_employee}}
                                            @else
                                        {{'Nessuno'}}
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grigio shadow destra">
                                        Mobile:&nbsp
                                    </td>
                                    <td class="fucsia ombra sinistra">
                                        @if($employee->cell_employee!=null)
                                        {{$employee->cell_employee}}
                                        @else
                                            {{'Nessuno'}}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        <br />
                        <div class="row center grigio shadow">
                            Vuoi aggiornare i tuoi dati?
                        </div><br />
                        <div class="row center">
                            <a  href="{{ route('upprofile') }}" class="form-group text-center btn btn-primary pulsante">
                                Aggiorna
                            </a>
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