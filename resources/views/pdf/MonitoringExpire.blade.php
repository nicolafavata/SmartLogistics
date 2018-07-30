<head>
    <title>Scadenze delle merci</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mystyle.css" rel="stylesheet">
</head>

<div class="container">

    <div class="text-center">
        <img src="img/logo.gif"><br /><br />
        <?php $today=date('d/m/Y')?>
        <h4 class="text-uppercase fucsia">Monitoraggio scadenza delle merci al {{$today}}</h4><br />
        <h5 class="text-uppercase font-weight-bold ">{{$rag_soc}}</h5>
    </div><br />

    <div class="text-left">

        <?php $cod=null; ?>
        @foreach ($items as $item)
            @if($item->cod_inventory!==$cod)
                <h5 class="fucsia font-weight-bold">{{'Codice: '.$item->cod_inventory.' - '.$item->title_inventory}}</h5>
                <h5>{{'In giacenza: '.$item->stock.' - Impegnati: '.$item->committed}}</h5>
                <table border="1" bgcolor="#f5f5dc">
                    <tr>
                        <td>Quantità</td>
                        <td>Unità di misura</td>
                        <td>Data di scadenza</td>
                    </tr>
                    <?php $cod = $item->cod_inventory; ?>
                    @foreach($items as $item2)
                        @if($item2->cod_inventory==$cod)
                            <tr>
                                <td class="text-center">{{$item2->stock_expiry}}</td>
                                <td class="text-center">{{$item2->unit_inventory}}</td>
                                <?php
                                $date=substr($item2->date_expiry,0,10);
                                $date=explode('-',$date);
                                $date=$date[2].'/'.$date[1].'/'.$date[0];
                                ?>
                                <td class="text-center">{{$date}}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                <br />
            @endif

        @endforeach

    </div>
</div>