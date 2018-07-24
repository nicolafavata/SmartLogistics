@extends('layouts.supplies')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner expires_img">
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
                <div class="col-md-12 jumbotron-expires border">
                    <div class="row">
                        <div class="col-md-8 text-center">
                            <h4 class="font-weight-bold text-dark shadow capitalize">Specifica le scadenze dei prodotti</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="icon-list">
                                <a href="{{route('store_expires')}}" title="Aggiungi le scadenze dei prodotti"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Per caricare le scadenze dei prodotti puoi scaricare e compilare il file <a href="{{route('download-expires-import')}}" class="text-success">expires_data.csv</a>.</p>

                                        <p>Compila il file con le informazioni dei tuoi prodotti. Le colonne sono tutte obbligatorie: </p>
                                        <ul>
                                            <li><span class="fucsia">cod_inventory</span>:&#09Il codice del prodotto deve essere presente nell'inventario ed essere univoco.</li>
                                            <li><span class="fucsia">quantity</span>:&#09Il quantitativo con una determinata scadenza.</li>
                                            <li><span class="fucsia">expire_date</span>:&#09La data di scadenza della quantità specificata per quel determinato prodotto.</li>
                                        </ul>
                                    <p>Ad esempio per il codice prodotto: 7 - Se la giacenza è di 20 prodotti dovrò specificare nel file: </p>
                                    <table class="table border">
                                        <thead>
                                            <tr>
                                                <td>
                                                    cod_inventory
                                                </td>
                                                <td>
                                                    quantity
                                                </td>
                                                <td>
                                                    expire_date
                                                </td>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>
                                                7
                                            </td>
                                            <td>
                                                10
                                            </td>
                                            <td>
                                                12/10/2018
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                7
                                            </td>
                                            <td>
                                                10
                                            </td>
                                            <td>
                                                12/01/2019
                                            </td>
                                        </tr>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                <ul>
                                    <li><span class="fucsia">ATTENZIONE!!!</span>:&#09Ogni codice inserito nel file deve rispettare il vincolo sulla quantità. Il totale delle quantità inserite deve essere uguale al totale della giacenza in magazzino, altrimenti l'operazione non va a buon fine. Ogni codice processato sostituisce le informazioni attuali, con quelle presenti nel file.</li>
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
                                        <a href="{{route('store_expires')}}" title="Aggiungi le scadenze dei prodotti"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
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