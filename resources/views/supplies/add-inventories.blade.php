@extends('layouts.supplies')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner inventories_img">
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
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi prodotti all'inventario</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="icon-list">
                                <a href="{{route('store_item')}}" title="Aggiungi nuovi prodotti"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Per caricare nuovi prodotti nell'inventario puoi scaricare e compilare il file <a href="{{route('download-products-import')}}" class="text-success">products_import.csv</a>.</p>

                                        <p>Compila il file con le informazioni dei tuoi prodotti. Le colonne obbligatorie sono: </p>
                                        <ul>
                                            <li><span class="fucsia">cod_inventory</span>:&#09Il codice del prodotto deve essere univoco. Se inserisci più di un prodotto con lo stesso codice verrà memorizzato solo il primo processato.</li>
                                            <li><span class="fucsia">title_inventory</span>:&#09Il titolo del prodotto.</li>
                                            <li><span class="fucsia">unit_inventory</span>:&#09Unità di misura. Cliccando in basso accedi alla lista delle  unità di misura supportate.</li>
                                            <li><span class="fucsia">stock</span>:&#09La quantità disponibile in magazzino.</li>
                                            <li><span class="fucsia">codice_iva_inventory</span>:&#09Devi inserire il codice iva del prodotto, Cliccando in basso accedi alla lista dei codici iva, prendi nota di quale utilizzare.</li>
                                            <li><span class="fucsia">expire_inventory</span>:&#09Inserisci "1" se il prodotto è con scadenza e vuoi monitorarla.</li>
                                            <li><span class="fucsia">sale_inventory</span>:&#09Inserisci "1" se si tratta di un prodotto finale destinato alla rivendita. Inserisci "0" se si tratta di una materia prima che utilizzi soltanto per la produzione di prodotti finali.</li>
                                        </ul>
                                    <!-- Unità di misura -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                                        Unità di misura
                                    </button>
                                    <!-- Codici Iva -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">Codici Iva</button>

                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Altre informazioni che puoi inserire sono: </p>
                                    <ul>
                                        <li><span class="text-success font-weight-bold">category_first, category_second</span>:&#09La categoria principale e quella secondaria.</li>
                                        <li><span class="text-success font-weight-bold">description_inventory, brand</span>:&#09La descrizione del prodotto e il marchio.</li>
                                        <li><span class="text-success font-weight-bold">ean_inventory</span>:&#09Il codice a barre del prodotto, importante per diverse funzionalità.</li>
                                        <li><span class="text-success font-weight-bold">average_cost_inventory, last_cost_inventory</span>:&#09Rispettivamente il costo medio e l'ultimo costo d'acquisto.</li>
                                        <li><span class="text-success font-weight-bold">height_inventory, width_inventory, depth_inventory</span>:&#09Dimensione del prodotto specificando altezza, larghezza e profondità, in millimetri.</li>
                                        <li><span class="text-success font-weight-bold">weight_inventory</span>:&#09Il peso del prodotto, in grammi.</li>
                                        <li><span class="text-success font-weight-bold">url_inventory</span>:&#09L'indirizzo http dell'immagine del prodotto.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                <ul>
                                    <li>Una volta caricato il file, viene prenotata un operazione di caricamento massivo che sarà processata entro 24 ore.</li>
                                    <li>Puoi prenotare anche più operazioni nella stessa giornata.</li>
                                    <li>I prodotti vengono aggiunti a quelli esistenti.</li>
                                    <li>Dopo il caricamento del file si procede all'inizializzazione per la <span class="fucsia">previsione sulle vendite</span> dei prodotti caricati, le opzioni sono:
                                        <ul>
                                            <li class="text-success font-weight-bold">Caricamento dati storici. Devi compilare e caricare il file <a href="{{route('download-historical-data')}}" class="text-success">historical_data.csv</a> con le seguenti specifiche:</li>
                                            <ul>
                                                <li><span class="fucsia">sale_date</span>:&#09La data di vendita.</li>
                                                <li><span class="text-success">document_number</span>:&#09Il numero del documento, non è obbligatorio e non viene processato.</li>
                                                <li><span class="fucsia">cod_inventory</span>:&#09Il codice del prodotto venduto.</li>
                                                <li><span class="fucsia">quantity</span>:&#09La quantità venduta.</li>
                                            </ul>
                                            <li class="text-success font-weight-bold">Associazione previsione di un altro prodotto.</li>
                                            <li class="text-success font-weight-bold">Lancio di un nuovo prodotto.</li>
                                        </ul>
                                    </li>
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
                                        <a href="{{route('store_item')}}" title="Aggiungi nuovi prodotti"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <!-- Unità di misura -->
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalCenterTitle">Unità di misura</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-sm">
                                                <thead>
                                                <tr>
                                                    <th scope="col">Valore di: unit_inventory</th>
                                                    <th scope="col">Descrizione</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>NR</td>
                                                    <td>Relativo a prodotti di una singola unità</td>
                                                </tr>
                                                <tr>
                                                    <td>GR</td>
                                                    <td>Relativo a prodotti misurati con il peso, da utilizzare i grammi</td>
                                                </tr>
                                                <tr>
                                                    <td>ML</td>
                                                    <td>Relativo a prodotti liquidi, da utilizzare i milliletri</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Codici Iva -->
                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalCenterTitle">Prendi nota del codice iva</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-sm">
                                                <thead>
                                                <tr>
                                                    <th scope="col">Codice Iva</th>
                                                    <th scope="col">Imposta</th>
                                                    <th scope="col">Descrizione</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($iva as $codice)
                                                    <tr>
                                                        <td>{{$codice->codice_iva}}</td>
                                                        <td>{{$codice->imposta.' %'}}</td>
                                                        <td>{{$codice->descrizione}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
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