@extends('layouts.employee-sub')
@section('title','Modifica il fornitore')

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' puoi modificare i dati del tuo fornitore'}}</h2>
@endsection

@section('content_section')
        <div id="body_page" class="container-fluid home_employee employee_img">
                <div class="row">

                    <div class="col-md-12 jumbotron border employee">
                        <div>
                            <a class="fucsia" href="{{route('providers')}}">Indietro </a>
                        </div>
                        <br />
                        <form onsubmit="showloader()" method="POST" action="{{ route('update-provider-db') }}">
                                {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="id_provider" value="{{$provider->id_provider}}">
                            <div class="form-check">
                                <label class="grigio">Ragione Sociale:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input @if($provider->supply_provider=='1')
                                       disabled
                                       @endif required maxlength="50" placeholder="Ragione Sociale & C. S.n.C." type="text" name="rag_soc_provider" id="rag_soc_provider" class="font-weight-bold" value="{{old('rag_soc_provider',$provider->rag_soc_provider)}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Codice del fornitore:&nbsp</label>
                                <input @if($provider->supply_provider=='1')
                                       disabled
                                       @endif required maxlength="10" placeholder="Obbligatorio per il mapping" type="text" name="provider_cod" id="provider_cod" class="font-weight-bold" value="{{old('provider_cod',$provider->provider_cod)}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Partita Iva:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input @if($provider->supply_provider=='1')
                                       disabled
                                       @endif required maxlength="11" placeholder="Partita Iva" type="text" name="iva_provider" id="iva_provider" class="font-weight-bold uppercase form-group"
                                       value="{{old('iva_provider',$provider->iva_provider)}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Indirizzo / Città :&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input @if($provider->supply_provider=='1')
                                       disabled
                                       @endif maxlength="150" placeholder="Piazza Garibaldi, 1 - 90127 Palermo" type="text" name="address_provider" id="address_provider" class="font-weight-bold uppercase form-group"
                                       value="{{old('address_provider',$provider->address_provider)}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">Telefono:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input @if($provider->supply_provider=='1')
                                       disabled
                                       @endif maxlength="16" placeholder="0277841256" type="text" name="telefono_provider" id="telefono_provider" class="font-weight-bold uppercase form-group"
                                       value="{{old('telefono_provider',$provider->telefono_provider)}}" >
                            </div><br />
                            <div class="form-check">
                                <label class="grigio">E-Mail:&nbsp&&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                <input @if($provider->supply_provider=='1')
                                       disabled
                                       @endif required maxlength="255" id="email_provider" type="email_provider" class="form-control font-weight-bold lowercase" name="email_provider" value="{{ old('email_provider',$provider->email_provider) }}"  placeholder="example: info@email.it">
                            </div><br />
                                    <button @if($provider->supply_provider=='1')
                                            disabled
                                            @endif
                                            type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                        Aggiorna
                                    </button>
                                @if($provider->supply_provider=='1')
                                    <h4 class="fucsia shadow font-weight-bold">Le informazioni non possono essere modificate perchè l'azienda è in aggregazione Supply Chain</h4>
                                @endif
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