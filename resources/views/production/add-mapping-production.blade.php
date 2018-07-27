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
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi le composizioni della produzione</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="icon-list">
                                <a href="{{route('store-mapping-production')}}" title="Aggiungi le composizioni della produzione"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Per caricare nuove composizioni del catalogo puoi scaricare e compilare il file <a href="{{route('download-mapping-production-import')}}" class="text-success">mapping_production_import.csv</a>.</p>

                                        <p>Compila il file con le informazioni dei tuoi prodotti. Le colonne obbligatorie sono: </p>
                                        <ul>
                                            <li><span class="fucsia">cod_production</span>:&#09Il codice di produzione.</li>
                                            <li><span class="fucsia">title_production</span>:&#09Il titolo della produzione (non obbligatorio da compilare).</li>
                                            <li><span class="fucsia">cod_inventory</span>:&#09Il codice dell'inventario acquisti.</li>
                                            <li><span class="fucsia">title_inventory</span>:&#09Il titolo del prodotto dell'invenario (non obbligatorio da compilare).</li>
                                            <li><span class="fucsia">quantity</span>:&#09La quantità necessaria alla produzione.</li>
                                        </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <br>
                            <div class="row">
                                <div class="col-md-8 text-left">
                                    <p class="fucsia font-weight-bold shadow">Clicca il tasto avanti per proseguire con l'operazione.</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="icon-list">
                                        <a href="{{route('store-mapping-production')}}" title="Aggiungi le composizioni della produzione"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
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