@extends('layouts.productions')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner production_img">
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
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi fatture di vendita</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="icon-list">
                                <a href="{{route('store_invoice')}}" title="Aggiungi fatture"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Per caricare nuovi prodotti nel catalogo di produzione puoi scaricare e compilare il file <a href="{{route('download-production-import')}}" class="text-success">production_import.csv</a>.</p>
                                        <p>Compila il file con le informazioni dei tuoi prodotti. Le colonne obbligatorie sono: </p>
                                        <ul>
                                            <li><span class="fucsia">cod_production</span>:&#09Il codice del prodotto deve essere univoco. Il codice inserito deve essere diverso anche rispetto ai prodotti nell'inventario d'acquisto.Se inserisci più di un prodotto con lo stesso codice verrà memorizzato solo il primo processato.</li>
                                            <li><span class="fucsia">title_production</span>:&#09Il titolo del prodotto.</li>
                                            <li><span class="fucsia">unit_production</span>:&#09Unità di misura. Cliccando in basso accedi alla lista delle  unità di misura supportate.</li>
                                            <li><span class="fucsia">codice_iva_production</span>:&#09Devi inserire il codice iva del prodotto, Cliccando in basso accedi alla lista dei codici iva, prendi nota di quale utilizzare.</li>
                                        </ul>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 text-left">
                            <br>
                            <div class="row">
                                <div class="col-md-8 text-left">
                                    <p class="fucsia font-weight-bold shadow">Clicca il tasto avanti per proseguire con l'operazione.</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="icon-list">
                                        <a href="{{route('store_invoice')}}" title="Aggiungi fatture"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
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