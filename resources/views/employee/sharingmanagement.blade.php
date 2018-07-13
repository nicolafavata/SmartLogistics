@extends('layouts.employee-sub')
@section('title','Sede di '.$dati->rag_soc_company)

@section('content_header')
    <h2 class="shadow verdino center">{{$dati->name.' '.$dati->cognome.' puoi gestire le informazioni da condividere con'}}
    </h2>
    <h3 class="grigio uppercase font-weight-bold">{{$rag_soc->rag_soc_company}}</h3>
@endsection

@section('content_section')

    <div class="container-fluid home_employee scm_img2">
        <div class="row">
            <div class="col-md-12 jumbotron border employee">
                <div class="row">
                            <form onsubmit="showloader()" method="POST" action="{{ route('updatesupplychain') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PATCH">
                                <input type="hidden" name="company_supply_received" value="{{$rag_soc->id_company_office}}">
                                <div class="row">
                                    <label class="grigio capitalize">Siete in aggregazione Supply Chain dal:</label>
                                    <label class="fucsia font-weight-bold shadow">{{$supply->created_at->format('d/m/Y')}}</label>
                                </div>
                                <div class="row">
                                    <label class="grigio capitalize">L'ultima modifica è stata effettuata il:</label>
                                    <label class="fucsia font-weight-bold shadow">{{$supply->updated_at->format('d/m/Y')}}</label>
                                </div>
                                <hr>
                                <div class="row">
                                    <label class="fucsia font-weight-bold shadow">Se sei un cliente dell'azienda</label>
                                </div>
                                <br>
                                <div class="row">
                                    <input type="checkbox" name="forecast" value="1"
                                    @if($supply->forecast=='1')
                                        checked
                                           @endif
                                    >
                                    <label class="grigio font-weight-bold">Condividi le previsioni sulle tue vendite</label>
                                </div>
                                <div class="row">
                                    <input type="checkbox" name="ean_mapping" value="1"
                                           @if($supply->ean_mapping=='1')
                                           checked
                                            @endif
                                    >
                                    <label class="grigio font-weight-bold">Mappa i prodotti tramite codice a barre EAN</label>
                                </div>
                                <hr>
                                <br />
                                <div class="row">
                                    <label class="fucsia font-weight-bold shadow">Se sei un fornitore dell'azienda</label>
                                </div>
                                <br />
                                <div class="row">
                                    <input type="checkbox" name="availability" value="1"
                                           @if($supply->availability=='1')
                                           checked
                                            @endif
                                    >
                                    <label class="grigio font-weight-bold">Mostra la giacenza effettiva del tuo magazzino</label>
                                </div>
                                <div class="row">
                                    <input type="checkbox" name="b2b" value="1"
                                           @if($supply->b2b=='1')
                                           checked
                                            @endif
                                    >
                                    <label class="grigio font-weight-bold">Mostra i prezzi riservati B2B</label>
                                </div>
                                <hr>
                                <div class="row">
                                        <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                            Aggiorna aggregazione
                                        </button>
                                </div>


                            </form>
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