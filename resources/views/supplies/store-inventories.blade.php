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
                        <div class="col-md-12 text-left">
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi prodotti all'inventario</h4>
                        </div>
                        <form onsubmit="showloader()" method="POST" action="{{ route('upload-inventories') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-12 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="verde font-weight-bold shadow">Carica il file dei prodotti</h3>
                                        </div>
                                        <input type="file"  name="inventory" id="inventory" class="form-control">
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-12 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="verde font-weight-bold shadow">Inizializzazione previsione</h3>

                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input value="historical" onclick="showfile('file-historical','select-forecast')" type="radio" name="initial" id="historical" aria-label="Radio button for following text input">
                                                    </div>
                                                    <label class="form-control"> Carica file dati storici</label>
                                                </div>
                                                <div id="file-historical" hidden>
                                                    <input type="file"  name="file-historical" id="inventory" class="form-control">
                                                </div>
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input value="forecast" onclick="showfile('select-forecast','file-historical')" type="radio" name="initial" id="forecast" aria-label="Radio button for following text input">
                                                    </div>
                                                    <label class="form-control"> Previsione di un prodotto già esistente</label>
                                                </div>
                                                <div id="select-forecast" hidden>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="inputGroupSelect01">Prodotto</label>
                                                        </div>
                                                        @foreach($items as $item)
                                                            <select name='id_inventory' class="custom-select" id="inputGroupSelect01">
                                                                <option value="{{$item->id_inventory}}">{{$item->title_inventory}}</option>
                                                            </select>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input value="new" onclick="hiddenfile('file-historical','select-forecast')" type="radio" checked name="initial" id="new" aria-label="Radio button for following text input">
                                                    </div>
                                                    <label class="form-control"> Lancio di un nuovo prodotto</label>
                                                </div>


                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <button type="submit" class="text-center btn btn-primary" id="submit_picture">
                                        INVIA
                                    </button>
                                </div>

                        </form>




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