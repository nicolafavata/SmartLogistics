@extends('layouts.employee')
@section('title','Smartlogis per le aziende')


@section('content_section')
        <div class="container-fluid home_employee research_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        @foreach($business as $found)
                            @if($found->id_company_office!=$dati->id_company_office)
                                <hr>
                                <div class="row">
                                    <h3 class="verde font-weight-bold shadow uppercase">{{$found->rag_soc_company}}</h3>
                                    <h5 class="fucsia shadow">
                                        Responsabile:{{' '.$found->name.' '.$found->cognome}}
                                    </h5>
                                    <h6 class="grigio shadow">{{$found->indirizzo_company.', '.$found->civico_company}}</h6>
                                    <h6 class="grigio shadow">
                                        @if($found->cap=='8092')
                                           {{$found->cap_company_office_extra.' '.$found->city_company_office_extra.' '.$found->state_company_office_extra.' '.$found->nazione_company}}
                                        @else
                                            {{$found->cap.' - '.$found->comune.' ('.$found->sigla_prov.')'}}
                                        @endif
                                    </h6>
                                    <h6 class="grigio">
                                        {{'Tel: '.$found->telefono_company.' | Fax: '.$found->fax_company.' | Email: '.$found->email_company}}
                                    </h6>
                                    <form onsubmit="showloader()" method="POST" action="{{ route('request-supply', $found->id_company_office) }}">
                                        {{ csrf_field() }}
                                            <button type="submit" class="btn btn-primary pulsante">Richiedi aggregazione</button>
                                    </form>
                                </div>
                                <hr>
                            @endif
                        @endforeach

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