@extends('layouts.employee')
@section('title','Smartlogis per le aziende')


@section('content_section')
        <div class="container-fluid home_employee expires_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <div class="row">
                                 <form onsubmit="showloader()" method="POST" action="{{ route('setting-expire') }}">
                                    {{ csrf_field() }}
                                    <div class="center">
                                        <h2 class="fucsia shadow">Monitoraggio delle scadenze dei prodotti</h2>
                                    </div>
                                        <input type="hidden" name="_method" value="PATCH">
                                        <div class="form-check">
                                            <label class="grigio">Giorni di preavviso:&nbsp</label>
                                            <input placeholder="Giorni d'avvertimento" type="number" name="days_batchExpMon" id="days_batchExpMon" class="font-weight-bold" value="{{old('days_batchExpMon',$days)}}" >
                                        </div><br />
                                        <div>
                                            <input type="checkbox" @if($warned=="1") checked @endif  name="warned" value="1"><label class="fucsia font-weight-bold">&nbsp;Avvertimi</label>
                                        </div>
                                        <div class="destra">
                                             <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                                 Conferma
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