<head>
    <title>Ordine acquisto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mystyle.css" rel="stylesheet">
    <style>
        div {margin: 2px}
    </style>
</head>

<div style="margin: 2px">
        <table>
            <tr>
                <td rowspan="5" class="text-left">
                    <img height="150px" width="150px"
                         @if(($supply->logo)!='0')
                         src="{{env('APP_URL').'/storage/'.$supply->logo}}">
                    @else
                        src="{{env('APP_URL').'/img/logo_business.jpg'}}">
                    @endif
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    <h4 class="font-weight-bold text-uppercase">{{$supply->rag_soc_shares}}</h4>
                </td>
            </tr>
            <tr>
                <td>
                    <h5 class="text-capitalize">{{$supply->indirizzo_shares.', '.$supply->civico_shares.' - '}}
                        @if($supply->cap_shares=='8092')
                            {{$supply->cap_extra1.' '.$supply->city_extra1.' '.$supply->state_extra1}}
                        @else
                            {{$supply->cap_shares.' '.$supply->comune_shares.', '.$supply->sigla_prov_shares}}
                        @endif
                    </h5>
                </td>
            </tr>
            <tr>
                <td class="text-left text-capitalize">
                    <h5>
                        {{'Telefono: '.$supply->telefono.' - Email: '.$supply->email_shares}}
                    </h5>
                </td>
            </tr>
            <tr>
                <td class="text-left text-capitalize">
                    <h5>
                        {{'Partita iva: '.$supply->partiva}}
                    </h5>
                </td>
            </tr>
        </table>
        <hr>
        <h5 class="font-weight-bold">{{'Ordine Numero: '.$document->number.' del '.$document->date}}</h5>
        <hr>
        <div>
            <h5 class="font-weight-bold">{{'Spett.le: '}} </h5>
            <h5 class="font-weight-bold text-uppercase">{{$supply->rag_soc_received}}</h5>
            <h6 class="text-capitalize">{{$supply->indirizzo_received.', '.$supply->civico_received}}
                @if($supply->cap_shares=='8092')
                    {{$supply->cap_received.' '.$supply->city_received.' '.$supply->state_received}}
                @else
                    @if($supply->cap_received!=='')
                        {{$supply->cap_received.' '.$supply->comune_received.', '.$supply->sigla_prov_received}}
                    @endif
                @endif
            </h6>
            <h6 class="text-left text-capitalize">{{'Telefono: '.$supply->telefono_provider.' - Email: '.$supply->email_received}}</h6>
            <h6 class="text-left text-capitalize">{{'Partita iva: '.$supply->iva_provider}}</h6>
        </div>
        <hr>
        <h6 class="font-weight-bold grigio shadow">{{'Stato dell\'ordine: '}}
        @if($document->state=='00') {{'Annullato'}} @endif
            @if($document->state=='01') {{'Generato in attesa di trasmissione'}} @endif
            @if($document->state=='10') {{'Trasmesso al fornitore, in attesa della merce'}} @endif
            @if($document->state=='11') {{'Ordine confermato merce arrivata'}} @endif
        </h6>
        <hr>
    <div class="text-left">
        <table border="2" bgcolor="#f8f8ff">
            <thead>
                <tr border="1" bgcolor="#ff1493">
                    <td class="text-center font-weight-bold text-white">
                        Vs. Codice
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        Descrizione
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        Ns. Codice
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        Q.tà
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        U.M.
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        Prezzo
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        Sconto
                    </td>
                    <td class="text-center font-weight-bold text-white">
                        Importo
                    </td>
                </tr>
            </thead>
            <tbody>
        @foreach ($items as $item)
            <tr >
                <td class="text-center text-uppercase">
                    {{$item->your_code}}
                </td>
                <?php $desc = substr($item->desc,0,35)?>
                <td class="text-center text-capitalize">
                    {{$desc}}
                </td>
                <td class="text-center text-uppercase">
                    {{$item->our_code}}
                </td>
                <td class="text-center font-weight-bold">
                    {{$item->quantity}}
                </td>
                <td class="text-center">
                    {{$item->unit}}
                </td>
                <td class="text-center font-weight-bold">
                    <?php $price = number_format($item->price_unit_no_tax,2, ',', ''); ?>
                    {{$price.' €'}}
                </td>
                <td class="text-center">
                    @if($item->discount>0)
                        <?php $discount = number_format($item->discount,2, ',', ''); ?>
                        {{$discount,' %'}}
                        @endif
                </td>
                <td class="text-center">
                    <?php
                     $tot = $item->price_unit_no_tax * $item->quantity;
                     if ($item->discount>0) $tot = $tot - (($tot * $item->discount)/100);
                     $tot = number_format($tot,2, ',', '');
                    ?>
                    {{$tot.' €'}}
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>
        <table border="1" bgcolor="#7cfc00">
            <tr>
                <td style="padding: 10px" class="text-left font-weight-bold">
                    <?php $tot_netto = number_format($document->total_no_tax,2, ',', ''); ?>
                    &nbsp;{{'Totale imponibile: '.$tot_netto.' €'}}
                </td>
            </tr>
        </table>
    </div>
    <br /><br />
    <footer class="text-center font-weight-bold">
        <small class="text-center">Realizzato con l'applicazione www.smartlogis.it</small><br />
        <img src="img/logo.gif" width="250px">
    </footer>
</div>