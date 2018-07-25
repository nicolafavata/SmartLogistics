@extends('layouts.supplies')
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
                        <div class="col-md-12 text-left">
                            <h4 class="font-weight-bold text-dark shadow capitalize">Aggiungi nuovi prodotti alla produzione</h4>
                        </div>
                        <form onsubmit="showloader()" method="POST" action="{{ route('upload-production') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-12 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="verde font-weight-bold shadow">Carica il file dei prodotti</h3>
                                        </div>
                                        <input type="file"  name="production" id="production" class="form-control">
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-12 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="verde font-weight-bold shadow">Inizializzazione previsione</h3>

                                                @if(count($items)>0)
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
                                                            <select name='id_production' class="custom-select" id="inputGroupSelect01">
                                                            @foreach($items as $item)
                                                                    <option value="{{$item->id_production}}">{{$item->cod_production.' - '.$item->title_production}}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input value="new" onclick="hiddenfile('file-historical','select-forecast')" type="radio" checked name="initial" id="new" aria-label="Radio button for following text input">
                                                    </div>
                                                    <label class="form-control"> Lancio di un nuovo prodotto</label>
                                                </div>
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input value="nothing" onclick="hiddenfile('file-historical','select-forecast')" type="radio" checked name="initial" id="nothing" aria-label="Radio button for following text input">
                                                    </div>
                                                    <label class="form-control"> Non monitorare la previsione su questo prodotto (Ad esempio nel caso di una combinazione di prodotti già destinati alla vendita)</label>
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