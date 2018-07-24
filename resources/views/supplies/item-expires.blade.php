@extends('layouts.employee-sub')
@section('title','Sede di '.$dati->rag_soc_company)

@section('content_header')
    <h2 class="shadow verdino center">{{$item->cod_inventory.' - '.$item->title_inventory}}</h2>
    <div class="center">
        <table class="table table-responsive">
            <tr>
                <td class="grigio shadow destra">
                    Categoria principale:&nbsp
                </td>
                <td class="fucsia ombra sinistra">
                    {{$item->category_first}}
                </td>
                <td rowspan="4">

                </td>
                <td rowspan="4">
                    <img class="border-inventory"
                         @if ($item->url_inventory=='0')
                         src="img/nofoto.jpg"
                         @else
                         src="{{$item->url_inventory}}"
                         @endif
                         width="140" height="140" alt="{{$item->title_inventory}}"/>
                </td>
    </tr>
    <tr>
        <td class="grigio shadow destra">
            Categoria secondaria:&nbsp
        </td>
        <td class="fucsia ombra sinistra">
            {{$item->category_second}}
        </td>
    </tr>
    <tr>
        <td class="grigio shadow destra">
            Brand:&nbsp
        </td>
        <td class="fucsia ombra sinistra">
            {{$item->brand}}
        </td>
    </tr>
    <tr>
        <td class="grigio shadow destra">
            EAN:&nbsp
        </td>
        <td class="fucsia ombra sinistra">
            {{$item->ean_inventory}}
        </td>
    </tr>
    </table>
    </div>

@endsection

@section('content_section')
        <div id="body_page" class="container-fluid home_employee">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <h2 class="shadow fucsia center">Situazione scadenze</h2>
                        <table>
                            <thead>
                                    <tr class="grigio">
                                        <th>
                                           Quantità -&nbsp
                                        </th>
                                        <th>
                                            Unità di misura -&nbsp
                                        </th>
                                        <th>
                                           Data di scadenza
                                        </th>
                                    </tr>
                            </thead>
                            @foreach($expire as $t)
                                <tr>
                                    <td class="center fucsia">
                                        {{$t->stock_expiry}}
                                    </td>
                                    <td class="center fucsia">
                                        {{$item->unit_inventory}}
                                    </td>
                                    <td class="center fucsia">
                                        <?php
                                            $data = substr($t->date_expiry,0,10);
                                            $data = explode("-",$data);
                                            $date = $data[2].'/'.$data[1].'/'.$data[0];?>
                                        {{$date}}
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                        <br />
                        <h3 class="grigio">
                            {{'Quantità in giacenza: '.$item->stock}}
                        </h3>
                        <div class="icon-list">
                            <a href="{{route('expires')}}" title="Elenco prodotti con scadenza"><i class="verdino fa fa-backward fa-3x"></i></a>
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