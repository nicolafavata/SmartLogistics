<head>
    <title>Condivisione della previsione</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mystyle.css" rel="stylesheet">
    <style>
        div {margin: 5px}
    </style>
</head>

<div style="margin: 3px">

    <div class="text-center" style="margin: 10px">
        <img src="img/logo.gif"><br /><br />
        <h4 class="text-uppercase fucsia">Condivisione previsione sulle vendite generata il {{$today}}</h4>
        <h4 class="text-uppercase fucsia">Valida per i prossimi 12 mesi</h4>
        <table class="text-center">
            <tr>
                <td class="text-left">
                    <h5 class="font-weight-bold text-left verde">{{'Rivenditore: '}} </h5>
                </td>
                <td class="text-right">
                    <h5 class="font-weight-bold verde">{{'Fornitore: '}} </h5>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    <h6 class="text-uppercase text-left">{{$supply->rag_soc_shares}}</h6>
                </td>
                <td class="text-right">
                    <h6 class="text-uppercase">{{$supply->rag_soc_received}}</h6>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    <h6 class="text-uppercase">{{$supply->indirizzo_shares.', '.$supply->civico_shares}}</h6>
                </td>
                <td class="text-right">
                    <h6 class="text-uppercase">{{$supply->indirizzo_received.', '.$supply->civico_received}}</h6>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    <h6 class="text-uppercase"> @if($supply->cap_shares=='8092')
                        {{$supply->cap_extra1.' '.$supply->city_extra1.' '.$supply->state_extra1}}
                    @else
                        {{$supply->cap_shares.' '.$supply->comune_shares.', '.$supply->sigla_prov_shares}}
                        @endif</h6>
                </td>
                <td class="text-right">
                    <h6 class="text-uppercase">@if($supply->cap_shares=='8092')
                        {{$supply->cap_received.' '.$supply->city_received.' '.$supply->state_received}}
                    @else
                        {{$supply->cap_received.' '.$supply->comune_received.', '.$supply->sigla_prov_received}}
                        @endif</h6>
                </td>
            </tr>
        </table>
    </div><br />
    <?php $month=date('m'); $month=$month+0;?>
    <div class="text-left">
        <table border="1" bgcolor="#f5f5dc">
            <thead>
                <tr>
                    <td class="text-center font-weight-bold">
                        Codice Rivenditore
                    </td>
                    <td class="text-center font-weight-bold">
                        Codice Fornitore
                    </td>
                    <td class="text-center font-weight-bold">
                        Descrizione
                    </td>
                    <td class="text-center font-weight-bold">
                        U.M.
                    </td>
                    <td class="text-center font-weight-bold">
                        In giacenza
                    </td>
                    <td class="text-center font-weight-bold">
                        Impegnati
                    </td>
                    <td class="text-center font-weight-bold">
                        In Arrivo
                    </td>
                    <td class="text-center font-weight-bold">
                        {{$month}}
                        <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        @if ($month>12) {{$month-12}}  @else {{$month}} @endif <?php $month++ ?>
                    </td>
                    <td class="text-center font-weight-bold">
                        Totale vendite
                    </td>
                </tr>
            </thead>
            <tbody>
        <?php $cod=null; ?>
        @foreach ($items as $item)
            <tr>
                <td class="text-center">
                    {{$item->cod_inventory}}
                </td>
                <td class="text-center">
                    {{$item->cod_mapping_inventory_provider}}
                </td>
                <td class="text-left">
                    {{$item->title_inventory}}
                </td>
                <td class="text-center">
                    {{$item->unit_inventory}}
                </td>
                <td class="text-center">
                    {{$item->stock}}
                </td>
                <td class="text-center">
                    {{$item->committed}}
                </td>
                <td class="text-center">
                    {{$item->arriving}}
                </td>
                <td class="text-center">
                    {{$item->p1}}
                    <?php $tot=$item->p1; ?>
                </td>
                <td class="text-center">
                    {{$item->p2}}
                    <?php $tot=$tot+$item->p2; ?>
                </td>
                <td class="text-center">
                    {{$item->p3}}
                    <?php $tot=$tot+$item->p3; ?>
                </td>
                <td class="text-center">
                    {{$item->p4}}
                    <?php $tot=$tot+$item->p4; ?>
                </td>
                <td class="text-center">
                    {{$item->p5}}
                    <?php $tot=$tot+$item->p5; ?>
                </td>
                <td class="text-center">
                    {{$item->p6}}
                    <?php $tot=$tot+$item->p6; ?>
                </td>
                <td class="text-center">
                    {{$item->p7}}
                    <?php $tot=$tot+$item->p7; ?>
                </td>
                <td class="text-center">
                    {{$item->p8}}
                    <?php $tot=$tot+$item->p8; ?>
                </td>
                <td class="text-center">
                    {{$item->p9}}
                    <?php $tot=$tot+$item->p9; ?>
                </td>
                <td class="text-center">
                    {{$item->p10}}
                    <?php $tot=$tot+$item->p10; ?>
                </td>
                <td class="text-center">
                    {{$item->p11}}
                    <?php $tot=$tot+$item->p11; ?>
                </td>
                <td class="text-center">
                    {{$item->p12}}
                    <?php $tot=$tot+$item->p12; ?>
                </td>
                <td class="text-center">
                    {{$tot}}
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>
</div>