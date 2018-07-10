@extends('layouts.employee')
@section('title','Smartlogis per le aziende')


@section('content_section')
        <div class="container-fluid home_employee research_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <div class="row">
                                 <form onsubmit="showloader()" method="POST" action="{{ route('supplyfind') }}">
                                    {{ csrf_field() }}
                                    <div class="center">
                                        <h2 class="fucsia shadow">Digita la partita iva da ricercare</h2>
                                        <input type="text" name="research" maxlength="11">
                                        <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                            Ricerca
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