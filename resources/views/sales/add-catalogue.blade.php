@extends('layouts.sales')
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
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi i listini di vendita e la visibilità</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="icon-list">
                                <a href="{{route('store-catalogue')}}" title="Aggiungi i listini di vendita e la visibilità"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-left">
                            <div class="card pre-scrollable">
                                <div class="card-body">
                                    <p>Per caricare i listini di vendita e le informazioni sulla visibilità del catalogo puoi scaricare e compilare il file <a href="{{route('download-catalogue-import')}}" class="text-success">catalogue_import.csv</a>.</p>

                                        <p>Compila il file con le informazioni dei tuoi prodotti. Le colonne obbligatorie sono: </p>
                                        <ul>
                                            <li><span class="fucsia">cod_product</span>:&#09Il codice del prodotto (produzione o acquisti).</li>
                                            <li><span class="fucsia">title_product</span>:&#09Il titolo del prodotto (non è obbligatorio da compilare).</li>
                                            <li><span class="fucsia">price_user</span>:&#09Il prezzo applicato all'utente finale (iva esclusa).</li>
                                            <li><span class="fucsia">price_b2b</span>:&#09Il prezzo applicato ai rivenditori (iva esclusa).</li>
                                            <li><span class="fucsia">price_visible</span>:&#09Può assumere due valori:</li>
                                            <ul>
                                                <li>
                                                    <span class="verde font-weight-bold">"1"</span>:&#09Indica che il prezzo del prodotto è visibile nelle ricerche dei cittadini.
                                                </li>
                                                <li>
                                                    <span class="verde font-weight-bold">"0"</span>:&#09Indica che il prezzo del prodotto non è visibile nelle ricerche dei cittadini.
                                                </li>
                                            </ul>
                                            <li><span class="fucsia">quantity_visible</span>:&#09Può assumere due valori:</li>
                                            <ul>
                                                <li>
                                                    <span class="verde font-weight-bold">"1"</span>:&#09Indica che la quantità disponibile del prodotto è visibile nelle ricerche dei cittadini.
                                                </li>
                                                <li>
                                                    <span class="verde font-weight-bold">"0"</span>:&#09Indica che la quantità disponibile del prodotto non è visibile nelle ricerche dei cittadini.
                                                </li>
                                            </ul>
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
                                        <a href="{{route('store-catalogue')}}" title="Aggiungi i listini di vendita e la visibilità"><i class="text-success shadow fa fa-forward fa-3x"></i></a>
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