@extends('layouts.sub-supplies')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner supplies_img">
        <br />
        <div class="container admin_home">
            <div class="row">

                <div class="row">
                    @if(session()->has('message'))
                        @component('components.alert-success')
                            {{session()->get('message')}}
                        @endcomponent
                    @endif
                    @if(count($errors))
                        @component('components.show-errors')
                            {{$errors}}
                        @endcomponent
                    @endif
                </div>
                <div class="col-md-12 jumbotron-inventory border">
                    <div class="row">
                        <div class="col-md-8 text-center">
                            <h3 class="font-weight-bold text-dark shadow">Specifica il mapping con {{$providers->rag_soc_provider}}</h3>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="icon-list">
                                <a href="{{route('store_mapping',$providers->id_provider)}}" title="Aggiungi il mapping dei prodotti con {{$providers->rag_soc_provider}}"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Per caricare il mapping dei prodotti puoi scaricare e compilare il file <a href="{{route('download-mapping-import')}}" class="text-success">mapping_provider.csv</a>.</p>

                                        <p>Compila il file con le informazioni dei tuoi prodotti. Le colonne sono tutte obbligatorie: </p>
                                        <ul>
                                            <li><span class="fucsia">cod_inventory</span>:&#09Il codice del tuo prodotto presente nell'inventario.</li>
                                            <li><span class="fucsia">title_inventory</span>:&#09Il titolo descrittivo del prodotto (si può lasciare anche vuoto non è obbligatorio compilarlo). </li>
                                            <li><span class="fucsia">cod_provider</span>:&#09Il codice del prodotto utilizzato dal fornitore.</li>
                                            <li><span class="fucsia">price_provider</span>:&#09Il prezzo d'acquisto applicato dal tuo fornitore.</li>
                                            <li><span class="fucsia">first</span>:&#09Per indicare se relativamente al prodotto il fornitore è principale o secondario. <br />Può assumere due valori <span class="text-success font-weight-bold">'1'-></span>Fornitore principale, oppure <span class="text-success font-weight-bold">'0'-></span>Fornitore secondario.</li>
                                        </ul>
                                    </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                <ul>
                                    <li><span class="fucsia">ATTENZIONE!!!</span>:&#09Se un codice viene inserito due volte nel file, il sistema memorizza l'ultimo processato.</li>
                                    <li>Una volta caricato il file, viene prenotata un operazione di caricamento massivo che sarà processata entro 24 ore.</li>
                                    <li>Puoi prenotare anche più operazioni nella stessa giornata.</li>
                                </ul>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-8 text-left">
                                    <p class="fucsia font-weight-bold shadow">Clicca il tasto avanti per proseguire con l'operazione.</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="icon-list">
                                        <a href="{{route('store_mapping',$providers->id_provider)}}" title="Aggiungi il mapping dei prodotti con {{$providers->rag_soc_provider}}"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>


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