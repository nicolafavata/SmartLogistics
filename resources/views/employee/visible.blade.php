@extends('layouts.employee')
@section('title','Smartlogis per le aziende')


@section('content_section')
        <div class="container-fluid home_employee admin_viewcompany">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <div class="row">
                                 <form onsubmit="showloader()" method="POST" action="{{ route('changemyvisible') }}">
                                    {{ csrf_field() }}
                                    <div class="form-row">
                                        <div class="form-check">
                                            <input id="visibile_b2b" type="checkbox" name="visible_business" value="1" onclick="showb2b()"
                                            @if($dati->visible_business=='1')
                                                checked>
                                                <label id="b2b" class="grigio" for="">La tua azienda è visibile alle altre aziende</label>
                                            @else
                                                >
                                                <label id="b2b" class="grigio" for="">Rendi visibile la tua azienda alle altre aziende</label>
                                            @endif
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-row">
                                        <div class="form-check">
                                            <input id="visible_user" type="checkbox" name="visible_user" value="1" onclick="showid('visible_user','comuni','visibile')"
                                                   @if($dati->visible_user=='1')
                                                   checked>
                                            <label id="visibile" class="grigio" for="">La tua azienda è visibile ai cittadini</label>
                                            @else
                                                >
                                                <label id="visibile" class="grigio" for="">Rendi visibile la tua azienda ai cittadini e seleziona i comuni di visibilità</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="comuni"
                                        @if($dati->visible_user=='0')
                                            style="display: none"
                                         @endif
                                            >

                                            <h3 class="fucsia shadow font-weight-bold">Puoi selezionare i comuni della provincia di {{$dati->provincia}}</h3>

                                            @foreach($comuni as $com)

                                                    <div class="form-row">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="cap_visible[]" value="{{$com->id_comune}}"
                                                                   @if($dati->visible_user=='1')
                                                                       @foreach($visibili as $vis)
                                                                            @if($com->id_comune==$vis->id_comune)
                                                                                checked
                                                                            @endif
                                                                       @endforeach
                                                                    @endif
                                                            >
                                                            <label class="grigio" for="">{{$com->cap.' '.$com->comune}}</label>
                                                        </div>
                                                    </div>
                                            @endforeach

                                    </div>
                                    <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                        Aggiorna
                                    </button>

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