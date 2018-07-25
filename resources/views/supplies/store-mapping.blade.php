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
                        <form onsubmit="showloader()" method="POST" action="{{ route('upload-mapping',$providers->id_provider) }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-12 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="verde font-weight-bold shadow">Carica il file con il mapping di {{$providers->rag_soc_provider}}</h3>
                                        </div>
                                        <input type="file"  name="mapping" id="mapping" class="form-control">
                                    </div>
                                </div>
                                <hr>

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