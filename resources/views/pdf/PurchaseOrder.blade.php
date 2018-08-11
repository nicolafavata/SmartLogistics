<head>
    <title>Ordine acquisto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mystyle.css" rel="stylesheet">
    <style>
        div {margin: 5px}
    </style>
</head>

<div style="margin: 3px">


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
                    <h5 class="text-uppercase"{{$supply->rag_soc_shares}}></h5>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="text-capitalize"{{$supply->indirizzo_shares.', '.$supply->civico_shares}}></h6>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    <h6 class="text-capitalize"> @if($supply->cap_shares=='8092')
                            {{$supply->cap_extra1.' '.$supply->city_extra1.' '.$supply->state_extra1}}
                        @else
                            {{$supply->cap_shares.' '.$supply->comune_shares.', '.$supply->sigla_prov_shares}}
                        @endif</h6>
                </td>
            </tr>
            <tr>
                <td class="text-left text-capitalize">
                    <h6>{{'Telefono:'.$supply->telefono.' - Email: '.$supply->email_shares}}</h6>
                </td>
            </tr>
            <tr>
                <td class="text-left text-capitalize">
                    <h6>{{'Partita iva:'.$supply->partiva}}</h6>
                </td>
            </tr>
        </table>
        <hr>
        <h5 class="text-capitalize">{{'Ordine numero: '.$document->number.' del '.$document->date}}</h5>
        <hr>
        <table class="text-left">
            <tr>
                <td class="text-left">
                    <h5 class="font-weight-bold text-left verde">{{'Fornitore: '}} </h5>
                </td>
            </tr>
            <tr>
                <td class="text-right">
                    <h6 class="text-uppercase">{{$supply->rag_soc_received}}</h6>
                </td>
            </tr>
            <tr>
                <td class="text-right">
                    <h6 class="text-uppercase">{{$supply->indirizzo_received.', '.$supply->civico_received}}</h6>
                </td>
            </tr>
            <tr>
                <td class="text-right">
                    <h6 class="text-uppercase">@if($supply->cap_shares=='8092')
                        {{$supply->cap_received.' '.$supply->city_received.' '.$supply->state_received}}
                    @else
                        {{$supply->cap_received.' '.$supply->comune_received.', '.$supply->sigla_prov_received}}
                        @endif</h6>
                </td>
            </tr>
        </table>
    <div class="text-left">
        <table border="1" bgcolor="#fff8dc">
            <thead>
                <tr>
                    <td class="text-center font-weight-bold">
                        Vs. Codice
                    </td>
                    <td class="text-center font-weight-bold">
                        Descrizione
                    </td>
                    <td class="text-center font-weight-bold">
                        Ns. Codice
                    </td>
                    <td class="text-center font-weight-bold">
                        Q.tà
                    </td>
                    <td class="text-center font-weight-bold">
                        U.M.
                    </td>
                    <td class="text-center font-weight-bold">
                        Prezzo
                    </td>
                    <td class="text-center font-weight-bold">
                        Iva %
                    </td>
                    <td class="text-center font-weight-bold">
                        Sconto
                    </td>
                    <td class="text-center font-weight-bold">
                        Importo
                    </td>
                </tr>
            </thead>
            <tbody>
        @foreach ($items as $item)
            <tr>
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
                <td class="text-center">
                    {{$item->quantity}}
                </td>
                <td class="text-center">
                    {{$item->unit}}
                </td>
                <td class="text-center">
                    <?php $price = number_format($item->price_unit_no_tax,2, ',', ''); ?>
                    {{$price.' €'}}
                </td>
                <td class="text-center">
                    {{$item->tax}}
                </td>
                <td class="text-center">
                    @if($item->discount>0)
                        <?php $discount = number_format($item->discount,2, ',', ''); ?>
                        {{$discount,' %'}}
                        @endif
                </td>
                <td class="text-center">
                    <?php
                     $tot = $price * $item->quantity;
                     if ($item->discount>0) $tot = $tot - (($tot * $discount)/100);
                     $tot = number_format($tot,2, ',', '');
                    ?>
                    {{$tot.' €'}}
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>
        <table border="1" bgcolor="#fff8dc">
            <tr>
                <td class="text-left">
                    <?php $tot_netto = number_format($document->total_no_tax,2, ',', ''); ?>
                    {{'Totale netto: '.$tot_netto.' €'}}
                </td>
                <td class="text-center">
                    <?php $iva = number_format($document->tax,2, ',', ''); ?>
                    {{'Iva: '.$iva.' €'}}
                </td>
                <td class="text-right">
                    <?php
                        $tot_document = $document->total_no_tax + $document->tax;
                        $tot_document = number_format($tot_document, 2, ',', '');
                    ?>
                    {{'Totale documento: '.$tot_document.' €'}}
                </td>
            </tr>
        </table>
    </div>
</div>